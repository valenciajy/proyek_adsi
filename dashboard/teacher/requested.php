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
$requests = $teacher->getRequests();
$requests_json = json_encode($requests);

// page properties
$title = "Request Bimbingan";
$stylePath = ROOT_PATH . "/style/style.css";
$scriptPath = ROOT_PATH . "/script/script.js";
//$hasSearch = false;
$headline = "Request Bimbingan";
$headlineContent = null;


if (isset($_POST["rejectButton"])) {
    $requestId = $_POST["requestId"];
    $reason = $_POST["rejectReason"];
    $pertemuan->declinePertemuan($requestId, $reason);
}

if (isset($_POST["onlineButton"]) || isset($_POST["offlineButton"])) {
    $requestId = $_POST["requestId"];
    $media = isset($_POST["offlineButton"]) ? "Offline" : "Online";
    $pertemuan->acceptPertemuan($requestId, $media);
}

ob_start();
?>
<!-- content -->
<div class="p-4 mx-auto xl:max-w-[90%]">
    <div class="max-w-[50%] space-y-4">
        <?php
        $periods = ["07.30-08.30", "08.30-09.30", "09.30-10.30", "10.30-11.30", "11.30-12.30", "12.30-13.30", "13.30-14.30", "14.30-15.30", "15.30-16.30", "16.30-17.30", "17.30-18.30", "18.30-19.30", "19.30-20.30"];
        if(count($requests) > 0){
            foreach ($requests as $key => $request) {
                $requestId = $request["id"];
                $studentName = $request["name"];
                $date = new DateTime($request["date"]);
                $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                $formatter->setPattern("EEEE, d MMMM yyyy");
                $guidanceDate = $formatter->format($date);
                $timePeriod = $periods[$request["time_period"] - 1];
                include LOCAL_ROOT . '/components/student-request-card.php';
            }
        }else{
            ?>
                <p>Belum ada request yang masuk!</p>
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
        document.getElementById(`request-action-${requestId}`).innerHTML = ""
        if (button.value == "Accept") {
            const idInput = `<input type="hidden" name="requestId" value="${requestId}"/>`
            const onlineButton = `<input type="submit"  name="onlineButton" id="online-${requestId}" value="Online" onclick='handleSelectMedia(this)' class="cursor-pointer px-6 py-2 bg-[#78cf86]/50 rounded-full font-medium transition-all hover:scale-95 hover:bg-[#78cf86]"></input>`
            const offlineButton = `<input type="submit" name="offlineButton" id="offline-${requestId}" value="Offline" onclick='handleSelectMedia(this)' class="cursor-pointer px-6 py-2 bg-[#78cf86]/50 rounded-full font-medium transition-all hover:scale-95 hover:bg-[#78cf86]"></input>`
            document.getElementById(`request-action-${requestId}`).innerHTML += idInput
            document.getElementById(`request-action-${requestId}`).innerHTML += onlineButton
            document.getElementById(`request-action-${requestId}`).innerHTML += offlineButton
        } else {
            const idInput = `<input type="hidden" name="requestId" value="${requestId}"/>`
            const reasonInput = `<input type="text" name="rejectReason" id="reason-${requestId}" placeholder="Alasan" class="border p-2 rounded-lg"/>`
            const rejectButton = `<input type="submit" name="rejectButton" id="doReject-${requestId}" value="Reject" class="cursor-pointer px-6 py-2 bg-gray-400 rounded-full font-medium transition-all hover:scale-95 hover:bg-red-400"></input>`
            const cancelButton = `<input type="button" name="${requestId}" id="cancel-${requestId}" value="Cancel" onclick='handleCancelReject(this)' class="cursor-pointer px-6 py-2 bg-gray-400 rounded-full font-medium transition-all hover:scale-95"></input>`
            document.getElementById(`request-action-${requestId}`).innerHTML += idInput
            document.getElementById(`request-action-${requestId}`).innerHTML += reasonInput
            document.getElementById(`request-action-${requestId}`).innerHTML += rejectButton
            document.getElementById(`request-action-${requestId}`).innerHTML += cancelButton
        }
    }

    function handleSelectMedia(button) {
        document.getElementById(`request-${button.name}`).remove();
    }

    function handleCancelReject(button) {
        const requestId = button.name;
        document.getElementById(`request-action-${requestId}`).innerHTML = ""
        const acceptButton = `<input type="button" onclick="handleRequest(this)" value="Accept" name="${requestId}" id="accept-${requestId}" class="cursor-pointer px-6 py-2 bg-green-400 rounded-full font-medium transition-all hover:scale-95"/>`
        const rejectButton = `<input type="button" onclick="handleRequest(this)" value="Reject" name="${requestId}" id="reject-${requestId}" class="cursor-pointer px-6 py-2 bg-red-400 rounded-full font-medium transition-all hover:scale-95"/>`
        document.getElementById(`request-action-${requestId}`).innerHTML += acceptButton
        document.getElementById(`request-action-${requestId}`).innerHTML += rejectButton
    }
</script>