<?php

class Jadwal
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function inputJadwal($selected_date, $selected_time, $teacher_id)
    {
        // Validate selected time
        if (!$selected_time) {
            echo "<script> document.addEventListener('DOMContentLoaded', function() { showToast('Error', 'Waktu bimbingan harus dipilih!'); }); </script>";
            exit();
        }

        // Get the day of the week
        $date = new DateTime($selected_date);
        $day_of_week = $date->format('w');

        // Insert the schedule into the database
        $stmt = $this->conn->prepare("INSERT INTO schedules(day, time_period, lecturer_id) VALUES(?,?,?)");
        $stmt->bind_param("iii", $day_of_week, $selected_time, $teacher_id);

        if ($stmt->execute()) {
            echo "<script> document.addEventListener('DOMContentLoaded', function() { showToast('Success', 'Jadwal berhasil ditutup!'); }); </script>";
            echo "<script> setTimeout(()=>{ window.location.replace('/proyek-adsi/dashboard/teacher/input-schedule.php') },500)</script>";
        } else {
            echo "<script> document.addEventListener('DOMContentLoaded', function() { showToast('Error', 'Terjadi kesalahan saat menutup jadwal!'); }); </script>";
        }

        $stmt->close();
    }
}

?>
