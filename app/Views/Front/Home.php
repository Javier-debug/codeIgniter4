<?=$this->extend('Front/layout/main')?>

<?=$this->section('title')?>
Productos
<?=$this->endSection()?>

<?=$this->section('content')?>
<script>
  applyFilter = () => {
    if (document.getElementById("datePicker").value != '') {
      window.location.href = "http://localhost:8080/?date=" + document.getElementById("datePicker").value;
    }
  }

  window.onload = function () {

    var chart = new CanvasJS.Chart("chartContainer", {
      animationEnabled: true,
      title: {
        text: "Porcentaje de productos vendidos"
      },
      subtitles: [{
        text: "<?php echo $filter?>"
      }],
      data: [{
        type: "pie",
        yValueFormatString: "#,##0.00\"%\"",
        indexLabel: "{label} ({y})",
        dataPoints: <?php echo json_encode($chartData, JSON_NUMERIC_CHECK);?>
      }]
    });
    chart.render();

  }
</script>

<div class="container" style="display:flex; justify-content:center;">
  <a href="<?=base_url(route_to(''))?>" class="button is-danger" style="margin: 10px;">Limpiar filtro</a>
  <input id="datePicker" style="margin: 10px;" type="date">
  <button id="applyFilter" onclick="applyFilter()" class="button is-primary" style="margin: 10px;">Aplicar
    filtro</button>
</div>
<section class="section">
  <div class="columns is-multiline">
    <div class="column is-half">
      <div class="container">
        <div id="chartContainer" style="height: 370px; width: 100%;"></div>

        <br>
        <br>
        <h2 id="productTitle"><strong>Detalle de productos</strong></h2>
        <table class="table">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Total vendido</th>
              <th>Subtotal MXN</th>
              <th>Subtotal USD</th>
              <th>Subtotal BOB</th>
              <th>Subtotal EUR</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product):?>
            <tr>
              <th><?php echo $product->title;?></th>
              <td><?php echo $product->sale_count;?></td>
              <td><?php echo $product->sale_count * $product->price;?> MXN</td>
              <td><?php echo(($product->sale_count * $product->price)*$product->amountConverter->USD);?> USD</td>
              <td><?php echo(($product->sale_count * $product->price)*$product->amountConverter->BOB);?> BOB</td>
              <td><?php echo(($product->sale_count * $product->price)*$product->amountConverter->EUR);?> EUR</td>
            </tr>
            <?php endforeach;?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="column is-half">
      <div class="columns is-multiline">
        <?php foreach ($products as $product):?>
        <div class="column is-half">
          <div class="card">
            <div class="card-image">
              <figure class="image is-4by3">
                <img src="<?php echo $product->image_url;?>" alt="Placeholder image">
              </figure>
            </div>
            <div class="card-content" style="min-height: 400px;">
              <div class="media">

                <div class="media-content">
                  <p class="title is-4"><?php echo $product->title;?></p>
                  <p class="subtitle is-6" style="margin-top:0px; margin-bottom:1px;"><strong>Precio:</strong>
                    <?php echo $product->price;?></p>
                  <p class="subtitle is-6"><strong>Ventas totales:</strong> <?php echo $product->sale_count;?></p>
                </div>
              </div>

              <div class="content">
                <?php echo $product->short_description;?>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach;?>
      </div>
    </div>
  </div>
</section>

<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<?=$this->endSection()?>