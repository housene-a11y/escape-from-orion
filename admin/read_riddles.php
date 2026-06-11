<?php
// auteur: Mohamed
// functie: alle riddles bekijken

session_start();
require_once '../functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$riddles = getAllRiddles();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Riddles</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .admin-container {
      max-width: 900px;
      margin: 60px auto;
      background: rgba(5, 15, 30, 0.85);
      border: 1px solid #0288d1;
      border-radius: 16px;
      padding: 40px 35px;
    }
    h1 { color: #4fc3f7; margin-bottom: 24px; }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.9rem;
    }
    th {
      color: #90caf9;
      text-align: left;
      padding: 10px 12px;
      border-bottom: 1px solid #1565c0;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.8rem;
    }
    td {
      padding: 10px 12px;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      color: #cfd8dc;
      vertical-align: top;
    }
    tr:hover td { background: rgba(2, 136, 209, 0.05); }
    .badge {
      padding: 3px 10px;
      border-radius: 99px;
      font-size: 0.8rem;
      font-weight: bold;
    }
    .badge-1 { background: rgba(2, 136, 209, 0.2); color: #4fc3f7; }
    .badge-2 { background: rgba(76, 175, 80, 0.2); color: #a5d6a7; }
    .btn {
      padding: 5px 12px;
      border-radius: 6px;
      font-size: 0.85rem;
      text-decoration: none;
      display: inline-block;
      margin-right: 6px;
      transition: 0.2s;
      border: none;
      cursor: pointer;
    }
    .btn-edit {
      background: rgba(2, 136, 209, 0.2);
      color: #4fc3f7;
      border: 1px solid #1565c0;
    }
    .btn-edit:hover { background: rgba(2, 136, 209, 0.4); }
    .btn-delete {
      background: rgba(255, 50, 50, 0.15);
      color: #ff6b6b;
      border: 1px solid #ff4444;
    }
    .btn-delete:hover { background: rgba(255, 50, 50, 0.3); }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;