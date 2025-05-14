<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>SKS Etkinlik Takvimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <style>
        body { background-color: #eef7ff; }
        .container { max-width: 1200px; margin-top:0px; }
        #calendar { background-color: white; padding: 20px; border-radius: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h3 class="mb-0">SKS Etkinlik Takvimi</h3>
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <label for="facilityFilter" class="form-label me-2 mb-0">Tesis Seçiniz:</label>
            <select id="facilityFilter" class="form-select" style="min-width: 200px;">
                <option value="all">Tüm Tesisler</option>
                <?php
                $stmt = $pdo->query("SELECT id, name FROM facilities ORDER BY name");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div id="calendar"></div>

    <!-- Tüm tesisler için liste görünüm -->
    <div id="event-list-container" class="mt-4 d-none">
        <h5 class="text-primary">Etkinlik Listesi (Tüm Tesisler)</h5>
        <ul id="event-list" class="list-group"></ul>
    </div>

    <div id="eventList" class="mt-4">
        <h5 class="text-center">Etkinlik Listesi</h5>
        <ul id="eventListContent" class="list-group"></ul>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const facilitySelect = document.getElementById("facilityFilter");
        const eventListContainer = document.getElementById("event-list-container");
        const eventList = document.getElementById("event-list");

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'tr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            buttonText: {
                today: 'Bugün',
                month: 'Ay',
                week: 'Hafta',
                list: 'Liste'
            },
            events: function (fetchInfo, successCallback, failureCallback) {
                let url = '/events.php';
                if (facilitySelect.value && facilitySelect.value !== "all") {
                    url += '?facility_id=' + facilitySelect.value;
                }

                fetch(url)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },
            eventClick: function(info) {
                alert(info.event.title + "\n" + info.event.start.toLocaleString() + " - " + info.event.end.toLocaleString());
            },
            height: "auto"
        });

        calendar.render();

        facilitySelect.addEventListener("change", function () {
            const selected = facilitySelect.value;

            if (selected === "all") {
                calendarEl.style.display = "none";
                eventListContainer.classList.remove("d-none");
                loadEventList();
            } else {
                calendarEl.style.display = "block";
                eventListContainer.classList.add("d-none");
                calendar.refetchEvents();
            }
        });

        function loadEventList() {
            fetch('/events.php?all=true')
                .then(res => res.json())
                .then(events => {
                    eventList.innerHTML = "";

                    if (events.length === 0) {
                        eventList.innerHTML = "<li class='list-group-item'>Etkinlik bulunamadı.</li>";
                        return;
                    }

                    events.sort((a, b) => new Date(a.start) - new Date(b.start));

                    events.forEach(ev => {
                        const li = document.createElement("li");
                        li.className = "list-group-item";
                        li.innerHTML = `
                            <strong>${ev.title}</strong><br>
                            ${ev.start} → ${ev.end}<br>
                            <span class="text-muted">${ev.description || ""}</span>
                        `;
                        eventList.appendChild(li);
                    });
                });
        }

        loadEventList(); // İlk yükleme
    });
</script>
</body>
</html>
