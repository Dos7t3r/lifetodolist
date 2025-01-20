<?php
header('Content-Type: text/html; charset=utf-8');

function readJsonFile() {
    $jsonFile = __DIR__. '/data/life_events.json';
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        return json_decode($jsonContent, true)?? [];
    }
    return [];
}

$events = readJsonFile();

// 计算已完成和未完成项目的数量
$completedCount = 0;
$totalCount = count($events);
foreach ($events as $event) {
    if ($event['completed']) {
        $completedCount++;
    }
}
$uncompletedCount = $totalCount - $completedCount;

// 计算百分比
$completedPercentage = ($totalCount > 0)? ($completedCount / $totalCount) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>人生大事清单</title>
    <!-- 引用自定义字体 -->
    <link rel="stylesheet" href="styles.css">
    <style>
        @font-face {
            font-family: 'AaBeiYuLinShiDeXin';
            src: url('https://blog.iambzy.com/todolist/AaBeiYuLinShiDeXin-2.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>人生大事清单</h1>
        <!-- 显示已完成和未完成项目的数量及百分比，并居中 -->
        <p style="text-align: center;">已完成项目数量：<?= $completedCount ?>，未完成项目数量：<?= $uncompletedCount ?> （<?= $completedCount ?>/<?= $totalCount ?>）<?= $completedPercentage ?>%</p>
        <div class="event-grid">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card" data-id="<?= htmlspecialchars($event['id']) ?>" onclick="fetchEventDetails(<?= $event['id'] ?>)">
                        <div class="event-card-inner">
                            <div class="event-content">
                                <h3 class="event-name"><?= htmlspecialchars($event['name']) ?></h3>
                                <p class="event-description"><?= htmlspecialchars($event['description']) ?></p>
                                <div class="event-footer">
                                    <span class="event-status <?= $event['completed']? 'completed' : ''?>">
                                        <?= $event['completed']? '已完成' : '未完成'?>
                                    </span>
                                    <?php if ($event['completed'] && $event['completionDate']):?>
                                        <span class="completion-date">
                                            完成于: <?= htmlspecialchars($event['completionDate'])?>
                                        </span>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
            <?php else:?>
                <div class="no-events">暂无事项</div>
            <?php endif;?>
        </div>
    </div>

    <!-- 模态框 -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalEventName"></h2>
            <div class="modal-info">
                <p id="modalEventDescription"></p>
                <p id="modalEventStatus"></p>
                <p id="modalEventCompletionDate"></p>
                <div id="modalEventDetails" class="modal-details"></div>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('eventModal');

        // 关闭模态框的函数
        function closeModal() {
            modal.classList.remove("show");
            setTimeout(() => {
                modal.style.display = "none";
            }, 300);
        }

        // 获取事件详情的函数
        function fetchEventDetails(eventId) {
            fetch(`get_event_details.php?id=${eventId}`)
              .then((response) => response.json())
              .then((data) => {
                    // 设置模态框中各个元素的文本内容
                    document.getElementById("modalEventName").textContent = data.name;
                    document.getElementById("modalEventDescription").textContent = data.description;

                    const statusText = data.completed? "已完成" : "未完成";
                    const statusClass = data.completed? "completed" : "";
                    document.getElementById("modalEventStatus").innerHTML =
                      `状态: <span class="event-status ${statusClass}">${statusText}</span>`;

                    if (data.completed && data.completionDate) {
                        document.getElementById("modalEventCompletionDate").textContent = `完成时间: ${data.completionDate}`;
                    } else {
                        document.getElementById("modalEventCompletionDate").textContent = "";
                    }

                    document.getElementById("modalEventDetails").textContent = data.details;

                    // 显示模态框
                    modal.style.display = "block";
                    setTimeout(() => {
                        modal.classList.add("show");
                    }, 10);
                })
              .catch((error) => console.error("Error:", error));
        }
    </script>
</body>
</html>