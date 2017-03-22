<?php
require_once 'ti.php';
require_once 'token.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php startblock('title') ?><?php endblock()?></title>
</head>
<body>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navContent">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="/">O.P.E Creature Database</a>

    <div class="collapse navbar-collapse" id="navContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a href="/search" class="nav-link">Search</a>
            </li>
            <?php if(!$token_valid): ?>
            <li class="nav-item">
                <a href="/login" class="nav-link">Log In</a>
            </li>
            <?php else: ?>
                <?php if($current_user->getPermLevel() == 9): ?>
                    <li class="nav-item">
                        <a href="/admin" class="nav-link">Admin</a>
                    </li>
                <?php endif ?>
                <li class="nav-item">
                    <a href="/entity/create" class="nav-link">Create Entity</a>
                </li>
                <li class="nav-item">
                    <a href="/logout" class="nav-link">Logout</a>
                </li>
            <?php endif ?>
        </ul>
    </div>
</nav>
<?php
    startblock('body');
    endblock();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
<?php
    startblock('script');
    endblock();
?>
</body>
</html>