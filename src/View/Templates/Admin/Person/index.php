<section class="content-header">
  <h1>Personen</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="glyphicon glyphicon-dashboard"></i> Pereonen</a></li>
    <li class="active">Index</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Partei</th>
            <th>slug</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($this->persons as $person) : ?>
            <tr>
                <td class="primary">
                    <strong><a href="/admin/person/edit/<?php echo $person->id; ?>"><?php echo $person->name; ?></a></strong></td>
                <td><?php echo $person->party_slug; ?></td>
                <td><?php echo $person->slug; ?></td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

</section>
