<?php

 // Called via AJAX only when the Analytics tab is opened.
 

session_start();
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$cacheFile = sys_get_temp_dir() . '/vit_analytics_v2_' . md5(session_id()) . '.json';
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 60) {
    header('Content-Type: application/json');
    header('Cache-Control: private, max-age=60');
    readfile($cacheFile);
    exit();
}

require 'db.php';
$eventsRaw = $pdo->query("
    SELECT name, venue, faculty_coordinator, event_type, multiday, date, end_date
    FROM events ORDER BY date ASC, id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$historyRaw = $pdo->query("
    SELECT id, segregated_on, date_from, date_to, events
    FROM segregation_history ORDER BY segregated_on DESC
")->fetchAll(PDO::FETCH_ASSOC);

$schoolLabels = []; $schoolCounts = [];
try {
    $rows = $pdo->query("
        SELECT school_name, SUM(student_count) AS total
        FROM segregation_stats GROUP BY school_name ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        if ((int)$r['total'] > 0) {
            $schoolLabels[] = $r['school_name'];
            $schoolCounts[] = (int)$r['total'];
        }
    }
} catch (\Exception $e) {}

$eventParticipation = [];
try {
    $rows = $pdo->query("
        SELECT ss.event_name,
               SUM(ss.student_count)           AS total_students,
               COUNT(DISTINCT ss.school_name)  AS school_count,
               MIN(sh.date_from)               AS event_date,
               e.event_type                    AS event_type
        FROM segregation_stats ss
        JOIN segregation_history sh ON sh.id = ss.history_id
        LEFT JOIN events e ON e.name = ss.event_name
        GROUP BY ss.event_name, e.event_type
        ORDER BY total_students DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $eventParticipation[] = [
            'name'       => $r['event_name'],
            'count'      => (int)$r['total_students'],
            'schools'    => (int)$r['school_count'],
            'event_date' => $r['event_date'] ?? '',
            'event_type' => $r['event_type'] ?? '',
        ];
    }
} catch (\Exception $e) {}

$schoolDateStats = [];
try {
    $rows = $pdo->query("
        SELECT ss.school_name,
               SUM(ss.student_count) AS total,
               sh.date_from          AS date,
               e.event_type          AS event_type
        FROM segregation_stats ss
        JOIN segregation_history sh ON sh.id = ss.history_id
        LEFT JOIN events e ON e.name = ss.event_name
        GROUP BY ss.school_name, sh.date_from, e.event_type
        ORDER BY sh.date_from ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $schoolDateStats[] = [
            'school'     => $r['school_name'],
            'total'      => (int)$r['total'],
            'date'       => $r['date'],
            'event_type' => $r['event_type'] ?? '',
        ];
    }
} catch (\Exception $e) { $schoolDateStats = []; }

$eventsMonthly = array_fill(1, 12, 0);
$heatmapByDate = []; $venueCounts = []; $teamCounts = [];
foreach ($eventsRaw as $ev) {
    $ts = strtotime($ev['date']);
    $eventsMonthly[(int)date('n', $ts)]++;
    if ($ev['multiday']) {
        $endTs = strtotime($ev['end_date'] ?? $ev['date']);
        for ($d = $ts; $d <= $endTs; $d += 86400) {
            $dk = date('Y-m-d', $d);
            $heatmapByDate[$dk] = ($heatmapByDate[$dk] ?? 0) + 1;
        }
    } else {
        $dk = date('Y-m-d', $ts);
        $heatmapByDate[$dk] = ($heatmapByDate[$dk] ?? 0) + 1;
    }
    $v = trim($ev['venue'] ?? '');
    if ($v) $venueCounts[$v] = ($venueCounts[$v] ?? 0) + 1;
    $t = trim($ev['faculty_coordinator'] ?? '');
    if ($t) $teamCounts[$t] = ($teamCounts[$t] ?? 0) + 1;
}
arsort($venueCounts); arsort($teamCounts);

$segregMonthly = array_fill(1, 12, 0);
$runEventCounts = []; $segregatedEventNames = []; $lastSegOn = null; $segHistoryForJS = [];
foreach ($historyRaw as $h) {
    $segregMonthly[(int)date('n', strtotime($h['segregated_on']))]++;
    if (!$lastSegOn) $lastSegOn = $h['segregated_on'];
    $evArr = $h['events'] ? json_decode($h['events'], true) : [];
    foreach ($evArr as $he) {
        if (!empty($he['name'])) $segregatedEventNames[$he['name']] = true;
    }
    $runEventCounts[] = count($evArr);
    $evNamesForSeg = [];
    $evArrForSeg   = $h['events'] ? json_decode($h['events'], true) : [];
    foreach ($evArrForSeg as $he) {
        if (!empty($he['name'])) $evNamesForSeg[] = $he['name'];
    }
    $segHistoryForJS[] = [
        'segregated_on' => $h['segregated_on'],
        'date_from'     => $h['date_from'],
        'date_to'       => $h['date_to'],
        'event_names'   => $evNamesForSeg,
    ];
}

$totalSegRuns      = count($historyRaw);
$totalStudents     = array_sum($schoolCounts);
$avgStudentsPerRun = $totalSegRuns > 0 ? round($totalStudents / $totalSegRuns) : 0;
$avgEventsPerRun   = (!empty($runEventCounts) && $totalSegRuns > 0)
    ? round(array_sum($runEventCounts) / $totalSegRuns, 1) : 0;

$totalEventsAll = count($eventsRaw);
$pendingCount   = count(array_filter($eventsRaw, fn($ev) => !isset($segregatedEventNames[$ev['name']])));

$payload = [
    'segregMonthly'      => array_values($segregMonthly),
    'heatmapByDate'      => $heatmapByDate,
    'venueLabels'        => array_keys(array_slice($venueCounts, 0, 10, true)),
    'venueCounts'        => array_values(array_slice($venueCounts, 0, 10, true)),
    'teamLabels'         => array_keys(array_slice($teamCounts, 0, 10, true)),
    'teamCounts'         => array_values(array_slice($teamCounts, 0, 10, true)),
    'schoolLabels'       => $schoolLabels,
    'schoolCounts'       => $schoolCounts,
    'eventParticipation' => $eventParticipation,   // now includes event_type + event_date
    'schoolDateStats'    => $schoolDateStats,       // now includes event_type
    'totalSegRuns'       => $totalSegRuns,
    'totalPending'       => $pendingCount,
    'totalStudents'      => $totalStudents,
    'avgStudentsPerRun'  => $avgStudentsPerRun,
    'avgEventsPerRun'    => $avgEventsPerRun,
    'segHistory'         => $segHistoryForJS,
];

$json = json_encode($payload);
@file_put_contents($cacheFile, $json);
header('Content-Type: application/json');
header('Cache-Control: private, max-age=60');
echo $json;