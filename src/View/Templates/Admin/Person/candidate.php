<section class="content-header">
  <h1>Personen</h1>
  <ol class="breadcrumb">
    <li><a href="/admin/person/index"><i class="glyphicon glyphicon-dashboard"></i> Personen</a></li>
    <li class="active">Kandidaten zur BTW 2017</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

<h2>Direktkandidaten in den Wahlkreisen</h2>

<table class="table table-striped">
    <thead>
    <tr>
        <th></th>
        <?php foreach ($this->parties as $party) : ?>
            <th><?php echo $party->shortname; ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($this->electoraldistricts as $electoraldistrict) : ?>
        <tr>
            <td class="primary">
                <strong><a href="/admin/party/edit/<?php echo $electoraldistrict->id; ?>"><?php echo $electoraldistrict->id; ?></a></strong>
                <small><?php echo $electoraldistrict->name; ?></small>
                <?php // TODO: Bundesland via memcached ?>
            </td>
            <?php foreach ($this->parties as $party) : ?>
                <td>
                    <?php
                    if (array_key_exists($electoraldistrict->id, $this->candidateArray)) {
                        if (array_key_exists($party->slug, $this->candidateArray[$electoraldistrict->id])) {
                                echo $this->candidateArray[$electoraldistrict->id][$party->slug];
                        }
                    }
                    ?>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>

<h2>Listen</h2>


</section>
