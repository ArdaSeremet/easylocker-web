<?php
session_start();
if(isset($_SESSION['management'])) unset($_SESSION['management']);
header('Location: /');