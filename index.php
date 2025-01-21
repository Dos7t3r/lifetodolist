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

// 计算百分比，并使用round函数保留到整数
$completedPercentage = ($totalCount > 0)? round(($completedCount / $totalCount) * 100) : 0;
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
        <p style="text-align: center;">截止2025年1月21日<br>已完成（<?= $completedCount ?>/<?= $totalCount ?>）<?= $completedPercentage ?>%</p>
        <div class="event-grid">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card" data-id="<?= htmlspecialchars($event['id']) ?>">
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
            <span class="close">&times;</span>
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
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("eventModal");
            const modalContent = modal.querySelector(".modal-content");
            const closeBtn = document.querySelector(".close");
            const eventCards = document.getElementsByClassName("event-card");

            // 为每个事件卡片添加点击事件
            Array.from(eventCards).forEach((card) => {
                card.addEventListener("click", function () {
                    const eventId = this.getAttribute("data-id");
                    fetchEventDetails(eventId);
                });
            });

            // 关闭模态框
            function closeModal() {
                modal.classList.remove("show");
                setTimeout(() => {
                    modal.style.display = "none";
                }, 300);
            }

            // 优化：检查 closeBtn 是否存在，避免错误
            if (closeBtn) {
                closeBtn.onclick = closeModal;
            }

            // 点击模态框外部关闭
            window.onclick = (event) => {
                if (event.target === modal &&!modalContent.contains(event.target)) {
                    closeModal();
                }
            };

            // 获取事件详情
            function fetchEventDetails(eventId) {
                fetch(`get_event_details.php?id=${eventId}`)
                   .then((response) => response.json())
                   .then((data) => {
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
        });
    </script>
</body>
</html>
