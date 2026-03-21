<?php
session_destroy();
header('Location: ' . url('home'));
exit;
