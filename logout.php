<?php
session_start(); //sessionスタートしないとdestroyできない
session_destroy();
header("Location: login.php");
