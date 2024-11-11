<?php
include_once '../../config.php';
require_once(LOCAL_ROOT . "/dashboard/middleware.php");
require_once(LOCAL_ROOT . "/connection.php");
require_once(LOCAL_ROOT . "/Pertemuan.php");
require_once(LOCAL_ROOT . "/Teacher.php");

$user = $_SESSION["user"];
$teacher = new Teacher($conn, $user);
$pertemuan = new Pertemuan($conn, $user);

// fetch data
$schedule = $teacher->getSchedule();
$schedule_json = json_encode($schedule);

// page properties
$title = "Jadwal Bimbingan";
$stylePath = ROOT_PATH . "/style/style.css";
$scriptPath = ROOT_PATH . "/script/script.js";
//$hasSearch = false;
$headline = "Jadwal Bimbingan";
$headlineContent = null;


if (isset($_POST["rejectButton"])) {
    $requestId = $_POST["requestId"];
    $reason = $_POST["rejectReason"];
    
    // Panggil metode batalkanPertemuan dari objek pertemuan
    $pertemuan->batalkanPertemuan($requestId, $reason);
}

ob_start();
?>
<!-- content -->
<div class="p-4 mx-auto xl:max-w-[90%]">
    <div class="max-w-[50%] space-y-4">
        <?php
        $periods = ["07.30-08.30", "08.30-09.30", "09.30-10.30", "10.30-11.30", "11.30-12.30", "12.30-13.30", "13.30-14.30", "14.30-15.30", "15.30-16.30", "16.30-17.30", "17.30-18.30", "18.30-19.30", "19.30-20.30"];
        if (count($schedule) > 0) {
            foreach ($schedule as $key => $request) {
                $requestId = $request["id"];
                $studentName = $request["name"];
                $date = new DateTime($request["date"]);
                $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                $formatter->setPattern("EEEE, d MMMM yyyy");
                $guidanceDate = $formatter->format($date);
                $timePeriod = $periods[$request["time_period"] - 1];
                $status = $request["status"];
                $reason = $request["reason"];

                include LOCAL_ROOT . '/components/student-schedule-card.php';
            }
        } else {
        ?>
            <p>Belum ada jadwal!</p>
        <?php
        }
        ?>
    </div>
</div>
<?php
// convert content
$content = ob_get_clean();
// use template
include LOCAL_ROOT . '/templates/template.php';

?>


<script>
    function handleRequest(button) {
        const requestId = button.name;
        document.getElementById(`schedule-action-${requestId}`).innerHTML = ""

        const idInput = `<input type="hidden" name="requestId" value="${requestId}"/>`
        const reasonInput = `<input type="text" name="rejectReason" id="reason-${requestId}" placeholder="Alasan" class="border p-2 rounded-lg"/>`
        const rejectButton = `<input type="submit" name="rejectButton" id="doReject-${requestId}" value="Cancel Schedule" class="cursor-pointer px-6 py-2 bg-gray-400 rounded-full font-medium transition-all hover:scale-95 hover:bg-red-400"></input>`
        const cancelButton = `<input type="button" name="${requestId}" id="cancel-${requestId}" value="Back" onclick='handleBack(this)' class="cursor-pointer px-6 py-2 bg-gray-400 rounded-full font-medium transition-all hover:scale-95"></input>`
        document.getElementById(`schedule-action-${requestId}`).innerHTML += idInput
        document.getElementById(`schedule-action-${requestId}`).innerHTML += reasonInput
        document.getElementById(`schedule-action-${requestId}`).innerHTML += rejectButton
        document.getElementById(`schedule-action-${requestId}`).innerHTML += cancelButton
    }

    function handleBack(button) {
        const requestId = button.name;
        document.getElementById(`schedule-action-${requestId}`).innerHTML = ""
        const cancelButton = `<input type="button" onclick="handleRequest(this)" value="Cancel" name="${requestId}" id="cancel-${requestId}" class="cursor-pointer px-6 py-2 bg-red-400 rounded-full font-medium transition-all hover:scale-95"/>`
        document.getElementById(`schedule-action-${requestId}`).innerHTML += cancelButton
    }
</script>
