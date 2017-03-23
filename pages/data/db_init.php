<?php
/**
 * Used to initialize the database.
 * Creates all tables necessary for operation and prints a status report at the end
 */

require_once "db.php";
require_once "util.php";

try {
// Create entity table
    $create_entity = $db->prepare(
        "CREATE TABLE Entities (" .
        "Id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT, " .
        "Name VARCHAR(100) NOT NULL, " .
        "Description TEXT NOT NULL," .
        "Parent INT UNSIGNED," .
        "Published BOOLEAN NOT NULL DEFAULT FALSE," .
        "KEY Entity_Entity_Id_fk (Parent)," .
        "CONSTRAINT Entity_Entity_Id_fk FOREIGN KEY (Parent) REFERENCES Entities (Id) ON DELETE SET NULL," .
        "CONSTRAINT Entity_Name_Unique UNIQUE (Name))");

    $status_entity = $create_entity->execute() ? 'Success' : 'Error';
} catch (PDOException $e) {
    $status_entity = $e->getMessage();
}

try {
    // Create tag table
    $create_tags = $db->prepare(
            'CREATE TABLE Tags (' .
            'EntityId INT UNSIGNED NOT NULL,' .
            'Tag VARCHAR(60) NOT NULL,' .
            'CONSTRAINT Tags_EntityId_Tag_pk PRIMARY KEY (EntityId, Tag),' .
            'CONSTRAINT Tags_Entities_Id_fk FOREIGN KEY (EntityId) REFERENCES Entities (Id) ON DELETE CASCADE' .
            ')');

    $status_tags = $create_tags->execute();
} catch (PDOException $e) {
    $status_tag = $e->getMessage();
}

try {
// Create users table
    $create_users = $db->prepare(
        'CREATE TABLE Users (' .
        'Id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,' .
        'Username VARCHAR(60) NOT NULL,' .
        'Password CHAR(60) NOT NULL,' .
        'Email VARCHAR(255),' .
        'PermLevel INT(1) DEFAULT 0 NOT NULL,' .
        'RegisterDate DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,' .
        'CONSTRAINT uc_Email UNIQUE (Email),' .
        'CONSTRAINT uc_Username UNIQUE (Username));');

    $status_users = $create_users->execute() ? 'Success' : 'Error';
} catch (PDOException $e) {
    $status_users = $e->getMessage();
}

try {
// Create sessions table
    $create_sessions = $db->prepare(
        'CREATE TABLE Sessions (' .
        'UserId INT UNSIGNED NOT NULL PRIMARY KEY,' .
        'Token CHAR(32) NOT NULL,' .
        'SupplyDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,' .
        'CONSTRAINT Sessions_Users_Fk FOREIGN KEY (UserId) REFERENCES Users (Id) ON DELETE CASCADE);');

    $status_sessions = $create_sessions->execute() ? 'Success' : 'Error';
} catch (PDOException $e) {
    $status_sessions = $e->getMessage();
}

try {
// Create pictures table
    $create_pictures = $db->prepare(
        'CREATE TABLE Pictures (' .
        'Id CHAR(32) PRIMARY KEY NOT NULL,' .
        'EntityId INT UNSIGNED NOT NULL,' .
        'Caption TINYTEXT,' .
        'Name VARCHAR(60) NOT NULL,' .
        'CONSTRAINT Pictures_Entity_fk FOREIGN KEY (EntityId) REFERENCES Entities (Id))');

    $status_pictures = $create_pictures->execute() ? 'Success' : 'Error';
} catch (PDOException $e) {
    $status_pictures = $e->getMessage();
}

try {
// Create edit log table
    $create_edit_log = $db->prepare(
        'CREATE TABLE EditLog (' .
        'UserId INT UNSIGNED,' .
        'Time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,' .
        'EntityId INT UNSIGNED,' .
        'PictureId CHAR(32),' .
        'CONSTRAINT EditLog_PK PRIMARY KEY (UserId, Time),' .
        'CONSTRAINT EditLog_Entity_Id_fk FOREIGN KEY (UserId) REFERENCES Entities (Id),' .
        'CONSTRAINT EditLog_Picutres_Id_fk FOREIGN KEY (PictureId) REFERENCES Pictures (Id),' .
        'CONSTRAINT Edit_Log_Valid_Nullness CHECK (COALESCE(EntityId, PictureId) IS NOT NULL));');

    $status_edit_log = $create_edit_log->execute() ? 'Success' : 'Error';
} catch (PDOException $e) {
    $status_edit_log = $e->getMessage();
}

try {
    $create_emp_code = $db->prepare(
        'CREATE TABLE RegistrationCodes (' .
        'Code CHAR(16) PRIMARY KEY NOT NULL,' .
        'PermLevel INT(1) NOT NULL);'
    );

    $status_emp_code = $create_emp_code->execute() ? 'Success' : 'Error';
} catch (PDOException $e) {
    $status_emp_code = $e->getMessage();
}

try {
    $hash = password_hash('password', PASSWORD_BCRYPT);

    $create_admin_acct = $db->prepare("INSERT INTO Users (Id, Username, Password, Email, PermLevel) VALUES (0, 'admin', '{$hash}', 'admin@fake.email', 9);");
    $create_admin_acct->execute();
} catch (PDOException $e) {
    $admin_acct_err = $e->getMessage();
}

function dispTheme($msg) {
    if($msg == 'Success')
        return ' list-group-item-success';
    else
        return ' list-group-item-danger';
}
?>

<?php require 'base.php' ?>
<?php startblock('title') ?>DB Init<?php endblock() ?>

<?php startblock('body') ?>
<div class="container mt-4">
    <ul class="list-group">
        <li class="list-group-item<?= dispTheme($status_entity) ?>">Entities: <?= $status_entity ?></li>
        <li class="list-group-item<?= dispTheme($status_tags) ?>">Data: <?= $status_tags ?></li>
        <li class="list-group-item<?= dispTheme($status_users) ?>">Users: <?= $status_users ?></li>
        <li class="list-group-item<?= dispTheme($status_sessions) ?>">Sessions: <?= $status_sessions ?></li>
        <li class="list-group-item<?= dispTheme($status_pictures) ?>">Pictures: <?= $status_pictures ?></li>
        <li class="list-group-item<?= dispTheme($status_edit_log) ?>">EditLog: <?= $status_edit_log ?></li>
        <li class="list-group-item<?= dispTheme($status_emp_code) ?>">RegistrationCodes: <?= $status_emp_code ?></li>
    </ul>
    <?php if(!isset($admin_acct_err)): ?>
        <div class="alert alert-info mt-3">An admin account has been created. Username: admin Password: password</div>
    <?php else: ?>
        <div class="alert alert-danger mt-3">An error occured while creating the admin account! <?= $admin_acct_err ?></div>
    <?php endif ?>
</div>
<?php endblock() ?>