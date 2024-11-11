<?php

class Pertemuan {
    private $conn;
    private $user;

    public function __construct($conn, $user) {
        $this->conn = $conn;
        $this->user = $user;
    }

    public function cancelPertemuan($pertemuanId) {
        $stmt = $this->conn->prepare("DELETE FROM guidances WHERE id = ?");
        $stmt->bind_param("i", $pertemuanId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function declinePertemuan($requestId, $reason) {
        $stmt = $this->conn->prepare("UPDATE guidances SET status = 'Ditolak', reason = ? WHERE id = ?");
        $stmt->bind_param("ss", $reason, $requestId);
        if ($stmt->execute()) {
            echo "<script> document.addEventListener('DOMContentLoaded', function() { showToast('Success', 'Bimbingan berhasil dibatalkan!'); }); </script>";
            header("Location: /proyek-adsi/dashboard/teacher/schedule.php");
        } else {
            echo "<script> document.addEventListener('DOMContentLoaded', function() { showToast('Error', 'Terjadi kesalahan saat membatalkan bimbingan!'); }); </script>";
        }
        $stmt->close();
    }

    public function acceptPertemuan($requestId, $media) {
        $stmt = $this->conn->prepare("UPDATE guidances SET status = 'Diterima', media = ? WHERE id = ?");
        $stmt->bind_param("ss", $media, $requestId);
        if ($stmt->execute()) {
            echo "<script> document.addEventListener('DOMContentLoaded', function() { showToast('Success', 'Bimbingan berhasil diterima!'); }); </script>";
            header("Location: /proyek-adsi/dashboard/teacher/schedule.php");
        } else {
            echo "<script> document.addEventListener('DOMContentLoaded', function() { showToast('Error', 'Terjadi kesalahan saat menerima bimbingan!'); }); </script>";
        }
        $stmt->close();
    }

    public function requestPertemuan($teacher_id, $date, $timePeriod) {
        $stmt = $this->conn->prepare("INSERT INTO guidances(student_id, lecturer_id, media, status, reason, date, time_period) VALUES(?,?,null,'Pending',null,?,?)");
        $stmt->bind_param("isss", $this->user['id'], $teacher_id, $date, $timePeriod);
        return $stmt->execute();
    }

    public function batalkanPertemuan($requestId, $reason) {
        $stmt = $this->conn->prepare("UPDATE guidances SET status = 'Dibatalkan', reason = ? WHERE id = ?");
        $stmt->bind_param("ss", $reason, $requestId);
        if ($stmt->execute()) {
            echo "<script> document.addEventListener('DOMContentLoaded', function() { showToast('Success', 'Bimbingan berhasil dibatalkan!'); }); </script>";
            header("Location: /proyek-adsi/dashboard/teacher/schedule.php");
        } else {
            echo "<script> document.addEventListener('DOMContentLoaded', function() { showToast('Error', 'Terjadi kesalahan saat membatalkan bimbingan!'); }); </script>";
        }
        $stmt->close();
    }
}

?>
