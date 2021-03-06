<?php
/**
 * Admin page for administering the cms
 */
require_once 'data/token.php';

if(!$token_valid) {
redirect();
exit;
} else {
    $edits = [];
    $edits = getEdits(EDITS_TO_DISPLAY, 0);
    $codes = getRegistrationCodes();

    if($current_user == null || $current_user->getPermLevel() != 9) {
        redirect();
        exit;
    } else {
        $users = getUsers();
    }
}
?>

<?php include 'data/base.php' ?>
<?php startblock('title') ?>Administration<?php endblock() ?>

<?php startblock('navbar') ?>
    <li class="nav-item">
        <a class="nav-link" href="<?= SITE_PREFIX ?>/admin/entities">Entity List</a>
    </li>
<?php endblock() ?>

<?php startblock('body') ?>
<link rel="stylesheet" type="text/css" href="<?= SITE_PREFIX ?>/css/theme.default.min.css"/>

<div class="modal fade" id="reg-code-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="perm-level">Employee Clearance</label>
                <select id="perm-level" class="form-control">
                    <?php foreach (CLEARANCE_LEVELS_REV as $LEVEL => $NAME): ?>
                        <option value="<?= $LEVEL ?>"><?= $NAME ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button id="create-code" type="button" class="btn btn-primary">Create Code</button>
            </div>
        </div>
    </div>
</div>
<div class="container mt-3">
    <h4>Users</h4>
    <table id="user-table" class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Email</th>
                <th>Perm Level</th>
                <th>Register Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $usr): ?>
                <tr>
                    <th scope="row"><?= $usr->getId() ?></th>
                    <td><?= $usr->getUsername() ?></td>
                    <td><?= $usr->getEmail() ?></td>
                    <td><?= CLEARANCE_LEVELS_REV[$usr->getPermLevel()] ?></td>
                    <td><?= $usr->getRegisterDate() ?></td>
                    <td class="text-center"><a href="<?= SITE_PREFIX ?>/user/<?= $usr->getId() ?>/edit"><i class="fa fa-pencil" aria-hidden="true"></i></a></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <h4>Recent Edits</h4>
    <table id="edits-table" class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Time</th>
                <th>Edits</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($edits as $edit): ?>
                <tr>
                    <td><?= $edit->getUsername() ?></td>
                    <td><?= prettyTime($edit->getTime()) ?> (<?= $edit->getTime() ?>)</td>
                    <?php if($edit->getEntityId() != null): ?>
                        <td><a href="<?= SITE_PREFIX ?>/entity/<?= urlencode(getEntity($edit->getEntityId())->getName()) ?>">Entity</a></td>
                    <?php elseif($edit->getPictureId() != null): ?>
                        <td><a href="<?= SITE_PREFIX ?>/image/<?= $edit->getPictureId() ?>/edit">Picture</a></td>
                    <?php endif ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h4>Pending Codes</h4><span class="text-muted" style="cursor: pointer;" id="create-registration-code" data-toggle="modal" data-target="#reg-code-modal">Create New</span>
    <table class="table table-sm table-bordered">
        <thead>
        <tr>
            <th>Code</th>
            <th>Clearance Level</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($codes as $code): ?>
            <tr>
                <td><?= $code->getCode() ?></td>
                <td><?= CLEARANCE_LEVELS_REV[$code->getPermLevel()] ?></td>
                <td><i class="fa fa-times" style="cursor: pointer; color: #ff1c17;" onclick="deleteCode(<?= $code->getCode() ?>)"></i></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endblock() ?>
<?php startblock('script') ?>
<script type="text/javascript" src="<?= SITE_PREFIX ?>/js/jquery.tablesorter.js"></script>
<script>

    $(document).ready(function() {
        $('#user-table').tablesorter();
        $('#edits-table').tablesorter();
    });

    $('#create-code').click(function () {
        var data = "perm-level=" + $('#perm-level').val();

        var xhr = new XMLHttpRequest();

        xhr.addEventListener("readystatechange", function () {
            if (this.readyState === 4 && xhr.status === 200) {
                location.reload();
            } else {
                $('#reg-code-modal').modal('hide');
            }
        });

        xhr.open("POST", "<?= SITE_PREFIX ?>/api/create-registration-code");
        xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded");

        xhr.send(data);
    });

    function deleteCode(code) {
        var data = "code=" + code;

        var xhr = new XMLHttpRequest();

        xhr.addEventListener("readystatechange", function () {
            if (this.readyState === 4) {
                location.reload();
            }
        });

        xhr.open("POST", "<?= SITE_PREFIX ?>/api/delete-registration-code");
        xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded");

        xhr.send(data);
    }
</script>
<?php endblock() ?>
