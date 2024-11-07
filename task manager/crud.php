<?php

$host = "localhost";
$user = "root"; 
$pass = "";   
$db_name = "task";

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = $_POST['action'] ?? '';

if ($action == 'add') {
    $text = $_POST['text'];
    $due_date = $_POST['dueDate'] ?: date('Y-m-d H:i:s');
    $urgent = isset($_POST['urgent']) && $_POST['urgent'] == '1' ? 1 : 0; 

    $stmt = $conn->prepare("INSERT INTO tasks (text, due_date, urgent) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $text, $due_date, $urgent); 
    $stmt->execute();
    $stmt->close();

} elseif ($action == 'read') {
    $result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    echo json_encode($tasks);
} elseif ($action == 'update') {
    $id = $_POST['id'];
    $text = $_POST['text'];
    $due_date = $_POST['dueDate'];
    $completed = $_POST['completed'];
    $urgent = isset($_POST['urgent']) && $_POST['urgent'] == '1' ? 1 : 0;

    $stmt = $conn->prepare("UPDATE tasks SET text = ?, due_date = ?, completed = ?, urgent = ? WHERE id = ?");
    $stmt->bind_param("ssiii", $text, $due_date, $completed, $urgent, $id);
    $stmt->execute();
    $stmt->close();

}
elseif ($action == 'delete') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>
