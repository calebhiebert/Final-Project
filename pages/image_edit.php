<?php
/**
 * Page for editing a single image
 */
require_once 'data/token.php';

if(!$token_valid) {
    redirect();
    exit;
}

$imageId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$image = getImage($imageId);

if($image == null) {
    redirect();
    exit;
}

if($_POST) {
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $caption = trim(filter_input(INPUT_POST, 'caption', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    if(strlen($caption) > 255) {
        $cptMsg = 'The caption must not exceed 255 characters in length';
    } else if (strlen($caption) == 0) {
        $caption = null;
    }

    if(strlen($name) > 60) {
        $nmeMsg = 'The name must not exceed 60 characters in length';
    } else if (strlen($name) < 3) {
        $nmeMsg = 'The name must be longer than 3 characters';
    }

    if(!isset($cptMsg) && !isset($nmeMsg)) {
        $image->setName($name);
        $image->setCaption($caption);
        editImage($image);
        redirect('/entity/'.$image->getEntityId().'/images');
        exit;
    }
}

if(isset($_GET['delete'])) {
    deleteImage($image->getId());
    deleteImageFile($image->getId(), $image->getFileExt());
    redirect('/entity/'.$image->getEntityId().'/images');
    exit;
} else {

}
?>

<?php include 'data/base.php' ?>
<?php startblock('title') ?>Image<?php endblock(); ?>

<?php startblock('body') ?>
<div class="container mt-4">
    <div class="card mb-4">
        <img alt="<?= $image->getName() ?>" class="card-img img-fluid" src="<?= SITE_PREFIX ?>/images/<?= IMAGE_EDIT_PAGE_IMAGE_SIZE.'/'.$image->getId().'.'.$image->getFileExt() ?>">

        <div class="card-block">
            <form method="post">
                <fieldset class="form-group<?= isset($nmeMsg) ? ' has-danger' : '' ?>">
                    <label class="form-control-label" for="name">Name</label>
                    <input class="form-control" type="text" name="name" id="name" value="<?= $image->getName() ?>">
                    <span class="form-control-feedback"><?= isset($nmeMsg) ? $nmeMsg : '' ?></span>
                </fieldset>
                <fieldset class="form-group<?= isset($cptMsg) ? ' has-danger' : '' ?>">
                    <label for="caption">Caption</label>
                    <textarea id="caption" class="form-control" name="caption"><?= $image->getCaption() ?></textarea>
                    <span class="form-control-feedback"><?= isset($cptMsg) ? $cptMsg : '' ?></span>
                    <small class="form-text text-muted">255 Characters max</small>
                </fieldset>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>
<?php endblock() ?>
