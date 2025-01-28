<?php
    require "../dist/php/template/header.php";
    require "../dist/php/funzioni/ottieni_account.php";
?>
    <table class="table table-striped projects">
        <thead>
            <tr>
                <th style="width: 80%" class="text-center">Numero</th>
                <th style="width: 20%"> Azioni</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach(ottieni_account($_SESSION["id"]) as $telefono) { ?>
        <tr>
            <td>
                <?php echo $telefono; ?>
            </td>
            <td class="project-actions text-right">
                <a class="btn btn-info btn-sm" href="messaggi.php?telefono=<?php echo urlencode($telefono); ?>">
                    <i class="fas fa-envelope">
                    </i> Messaggi
                </a>
            </td>
            <td class="project-actions text-right">
                <a class="btn btn-danger btn-sm" href="account_elimina.php?telefono=<?php echo urlencode($telefono); ?>&redirect=account_gestisci.php">
                    <i class="fas fa-trash">
                    </i> Elimina
                </a>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
<?php
    require "../dist/php/template/footer.php";
?>