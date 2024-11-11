<?php

class Teacher
{
    private $conn;
    private $user;

    public function __construct($conn, $user)
    {
        $this->conn = $conn;
        if (is_array($user)) {
            $this->user = $user;
        } else {
            $this->user = $this->getUserById($user);
        }
    }

    private function getUserById($userId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM lecturers WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getRequests()
    {
        $stmt = $this->conn->prepare("SELECT g.id, s.name, g.date, g.time_period  
                                    FROM guidances g  
                                    JOIN students s ON s.id = g.student_id  
                                    WHERE g.lecturer_id = ? AND status = 'Pending'");
        $stmt->bind_param("i", $this->user['id']);
        $stmt->execute();
        $requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $requests;
    }

    public function getSchedule()
    {
        $stmt = $this->conn->prepare("SELECT g.id, s.name, g.date, g.time_period, g.media, g.status, g.reason  
                                    FROM guidances g  
                                    JOIN students s ON s.id = g.student_id  
                                    WHERE g.lecturer_id = ? AND (g.status = 'Diterima' OR g.status = 'Dibatalkan' OR g.status = 'Ditolak')");
        $stmt->bind_param("i", $this->user['id']);
        $stmt->execute();
        $requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $requests;
    }

    public function getJadwal()
    {
        $teacher_id = $this->user['id'];
        $stmt = $this->conn->prepare("SELECT * FROM schedules WHERE lecturer_id = ?");
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $schedules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $schedules;
    }

    public function getGuidance()
    {
        $teacher_id = $this->user['id'];
        $stmt = $this->conn->prepare("SELECT * FROM guidances WHERE lecturer_id = ? AND status = 'Diterima'");
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $guidances = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $guidances;
    }

}
?>
