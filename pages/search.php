<?php
/**
 * Allows users to search entities
 */
require_once "data/token.php";

$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
$query = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$resultsPerPage = filter_input(INPUT_GET, 'rpp', FILTER_VALIDATE_INT);

if($page == null)
    $page = 0;
else if ($page > 0)
    $page--;
else if ($page < 0)
    $page = 0;

if($resultsPerPage == null)
    $resultsPerPage = DEFAULT_RESULTS_PER_PAGE;
else if ($resultsPerPage < MINIMUM_RESULTS_PER_PAGE)
    $resultsPerPage = MINIMUM_RESULTS_PER_PAGE;
else if ($resultsPerPage > MAXIMUM_RESULTS_PER_PAGE)
    $resultsPerPage = MAXIMUM_RESULTS_PER_PAGE;

$searchResults = searchEntities($query);
?>

<?php include 'data/base.php' ?>
<?php startblock('title') ?>Entity Index<?php endblock() ?>

<?php startblock('body') ?>
    <div class="container mt-4" id="entity-list">
        <div class="card">
            <form class="card-block" action="<?= SITE_PREFIX ?>/search" method="get">
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="Search" value="<?= $query ?>">
                    <button type="submit" class="input-group-addon"><i class="fa fa-search" aria-hidden="true"></i></button>
                </div>
            </form>
        </div>
        <div class="list-group mt-2">
            <?php if(count($searchResults) == 0): ?>
                <span class="list-group-item">No results were found :(</span>
            <?php endif; ?>
            <?php for ($i = ($page * $resultsPerPage); $i < min(($page * $resultsPerPage) + $resultsPerPage, count($searchResults)); $i++): ?>
                <a class="list-group-item list-group-item-action" href="<?= SITE_PREFIX ?>/entity/<?= urlencode($searchResults[$i]['Name']) ?>"><?= $searchResults[$i]['Name'] ?></a>
            <?php endfor ?>
        </div>
        <?php if(ceil(count($searchResults) / $resultsPerPage) > 1): ?>
            <ul class="pagination justify-content-center mt-2">
                <?php for ($i = 0; $i < ceil(count($searchResults) / $resultsPerPage); $i++): ?>
                    <li class="page-item<?= $i == $page ? ' active' : '' ?>"><a href="<?= SITE_PREFIX ?>/search?query=<?= urlencode($query) ?>&page=<?= $i + 1 ?>&rpp=<?= $resultsPerPage ?>" class="page-link"><?= $i + 1 ?></a></li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endblock() ?>