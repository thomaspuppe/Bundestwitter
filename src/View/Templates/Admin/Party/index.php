<section class="content-header">
  <h1>Parteien</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="glyphicon glyphicon-dashboard"></i> Parteien</a></li>
    <li class="active">Index</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Accounts</th>
            <th>slug</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($this->parties as $party) : ?>
            <tr class="<?php echo $party->slug; ?>">
                <td class="primary">
                    <strong><a href="/admin/party/edit/<?php echo $party->id; ?>"><?php echo $party->name; ?></a></strong></td>
                <td></td>
                <td><?php echo $party->slug; ?></td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

</section>
