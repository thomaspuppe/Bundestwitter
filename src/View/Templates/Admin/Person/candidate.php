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

<div class="row">
<div class="col-md-12">
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Sammlung</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="row">
        <div class="col-md-12">
          <div class="progress-group">
            <span class="progress-text">Direktkandidaten</span>
            <span class="progress-number"><b><?php echo $this->countDirectCandidates; ?></b>/<?php echo $this->countDirectCandidateSlots; ?></span>

            <div class="progress sm">
              <div class="progress-bar progress-bar-aqua" style="width: <?php echo intval($this->countDirectCandidates*100/$this->countDirectCandidateSlots); ?>%"></div>
            </div>
          </div>
          <!-- /.progress-group -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <div class="box-footer">
      <div class="row">
        <?php foreach ($this->countListCandidates as $partySlug => $directCandidateCount) : ?>
            <div class="col-xs-6">
              <div class="description-block border-right">
                <span class="description-percentage text-green"><?php echo $partySlug; ?></span>
                <h5 class="description-header"><?php echo $directCandidateCount; ?></h5>
                <span class="description-text">LISTENPLÄTZE</span>
              </div>
            </div>
        <?php endforeach; ?>
      </div>
      <!-- /.row -->
    </div>
  </div>
  <!-- /.box -->
</div>
<!-- /.col -->
</div>

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
                <small><?php echo $electoraldistrict->name; ?> (<?php echo $electoraldistrict->getDistrict()->name; ?>)</small>
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
