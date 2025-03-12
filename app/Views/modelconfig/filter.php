<!-- this files should not be loaded unless it parent files(views/modelconfig/table) is loaded already -->
<?php $where = ''; ?>
<div style="margin-left: -1rem;">
  <form action="">
    <div class="row col-lg-12">
      <?php 
      if ($filter): ?>
        <?php foreach ($filter as $item): ?>
             <?php $display = (isset($item['filter_display'])&&$item['filter_display'])?$item['filter_display']:$item['filter_label']; ?>
              <?php 
                if (isset($_GET[$display]) && $_GET[$display]) {
                  $value = $this->db->escape_str($_GET[$display]);
                  $where.= $where?" and {$item['filter_label']}='$value' ":"where {$item['filter_label']}='$value' ";
                }
              ?>
            <div class="form-group mb-2">
              <div class="col-lg-12">
                <select class="form-control <?php echo isset($item['child'])?'autoload':'' ?>" name="<?php echo $display; ?>" id="<?php echo $display; ?>" <?php echo isset($item['child'])?"data-child='{$item['child']}'":""?> <?php echo isset($item['load'])?"data-load='{$item['load']}'":""?> >
                  <option value="">..select <?php echo removeUnderscore(rtrim($display,'_id')) ?>...</option>
                    <?php if (isset($item['preload_query'])&& $item['preload_query']): ?>
                      <?php echo buildOptionFromQuery($this->db,$item['preload_query'],null,isset($_GET[$display])?$_GET[$display]:''); ?>
                    <?php endif; ?>
                    <!-- end for the option value -->
                </select>
              </div>
            </div>

        <?php if ($search): ?>

          <?php 
            $filterLabel = ($searchPlaceholder) ? $searchPlaceholder : $search;
            $placeholder = implode(',', $filterLabel);
            $val = isset($_GET['q'])?$_GET['q']:'';
            $val = $this->db->escape_str($val);
           ?>
          <div class="row mx-0">
            <div class="form-control-wrap col-lg-12 mb-2">
              <input class="form-control" type="text" name="q" placeholder="<?php echo $placeholder; ?>" value="<?php echo $val; ?>">
            </div>
          </div>
        <?php endif; ?> <!-- end the search input -->

          <?php if ($search || $filter): ?>
            <div class="form-group col-lg-3 mb-3">
              <input type="submit" value="Filter" class="btn btn-dark btn-block">
            </div>
          <?php endif; ?> <!-- end submit filter -->
        <?php endforeach; ?> <!-- end foreach for filter looop -->
      <?php endif; ?> <!-- end if filter -->
    </div>
  </form>
</div>