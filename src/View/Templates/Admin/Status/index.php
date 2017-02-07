<section class="content-header">
  <h1>
    Status
  </h1>
  <ol class="breadcrumb">
    <li class="active"><i class="glyphicon glyphicon-dashboard"></i> Status</li>
  </ol>
</section>

<section class="content">

<div class="row">
    <div class="col-lg-3 col-xs-6">

          <div class="small-box <?php echo ($this->memcached_ok ? 'bg-green' : 'bg-red'); ?>">
            <div class="inner">
              <h3>Memcached</h3>
              <p><?php echo $this->memcached_message; ?></p>
            </div>
          </div>

    </div>
</div>

</section>
