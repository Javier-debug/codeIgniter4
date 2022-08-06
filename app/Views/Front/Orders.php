<?=$this->extend('Front/layout/main')?>

<?=$this->section('title')?>
Ordenes
<?=$this->endSection()?>

<?=$this->section('content')?>

<style>
  .is-scrollable {
    overflow-y: scroll;
    max-height: 600px;
  }
</style>

<div class="container" style="display:flex; justify-content:center;">
  <a href="<?=base_url(route_to('orders'))?>" class="button is-danger" style="margin: 10px;">Limpiar filtro</a>
  <input id="datePicker" style="margin: 10px;" type="date">
  <button id="applyFilter" onclick="applyFilter()" class="button is-primary" style="margin: 10px;">Aplicar
    filtro</button>
</div>
<div class="section">
  <div class="columns is-multiline">
    <div class="column is-two-thirds is-scrollable">
      <table class="table">
        <thead>
          <tr>
            <th>Codigo de orden</th>
            <th>Estado</th>
            <th>Ciudad</th>
            <th>Fecha y hora</th>
            <th>Total</th>
            <th>Detalle</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>Codigo de orden</th>
            <th>Estado</th>
            <th>Ciudad</th>
            <th>Fecha y hora</th>
            <th>Total</th>
            <th>Detalle</th>
          </tr>
        </tfoot>
        <tbody>
          <?php foreach ($orders as $order):?>
            <tr>
              <th><?php echo $order->order_code;?></th>
              <td><?php echo $order->state;?></td>
              <td><?php echo $order->city;?></td>
              <td><?php echo $order->last_update;?></td>
              <td><?php echo $order->total;?> MXN</td>
              <td>
                <button class="button is-primary modal-button" onclick="showModal('<?php echo $order->order_code;?>')" data-target="modal-<?php echo $order->order_code;?>">Ver Detalle</button>
                <div id="modal-<?php echo $order->order_code;?>" style="z-index: 100;"class="modal">
                  <div class="modal-background"></div>
                  <div class="modal-card">
                    <header class="modal-card-head">
                      <p class="modal-card-title">Orden: <?php echo $order->order_code;?></p>
                      <button class="delete" onclick="hideModal('<?php echo $order->order_code;?>')" aria-label="close"></button>
                    </header>
                    <section class="modal-card-body">
                      <div class="columns is-multiline">
                        <div class="column is-half">
                          <h1><strong>Localidad</strong></h1>
                          <label><strong>Estado</strong>: <?php echo $order->state;?> </label>
                          <br>
                          <label><strong>Ciudad</strong>: <?php echo $order->city;?> </label>
                          <br>
                          <label><strong>Calle</strong>: <?php echo $order->street_name;?> </label>
                          <br>
                          <label><strong>Codigo postal</strong>: <?php echo $order->zip_code;?> </label>
                          <br><br>

                          <h1><strong>Detalles de compra</strong></h1>
                          <label><strong>Fecha y hora</strong>: <?php echo $order->last_update;?> </label>
                          <br>
                          <label><strong>Subtotal</strong>: <?php echo $order->subtotal;?> MXN</label>
                          <br>
                          <label><strong>Descuento</strong>: <?php echo $order->discount;?> MXN</label>
                          <br>
                          <label><strong>Total</strong>; <?php echo $order->total;?> MXN</label>
                        </div>
                        <div class="column is-half">
                        <h1><strong>Productos</strong></h1>
                        <?php foreach($order->products as $product):?>
                          <div class="card">
                            <div class="card-content">
                              <div class="media">
                                <div class="media-left">
                                  <figure class="image is-48x48">
                                    <img src="<?php echo $product->image_url;?>" alt="Placeholder image">
                                  </figure>
                                </div>
                                <div class="media-content">
                                  <p class="title is-4"><?php echo $product->title;?></p>
                                </div>
                              </div>

                              <div class="content">
                                <label><strong>Precio</strong>: <?php echo $product->price;?> MXN</label>
                                <br>
                                <label><strong>Cantidad</strong>: <?php echo $product->qty;?></label>
                                <br>
                                <label><strong>Subtotal</strong>: <?php echo ($product->price * $product->qty);?> MXN</label>
                                <br>
                                <label><strong>Descuento</strong>: <?php echo $product->discount;?> MXN</label>
                                <br>
                                <label><strong>Total</strong>; <?php echo $product->total;?> MXN</label>
                                <br>
                              </div>
                            </div>
                          </div>
                          <br>
                        <?php endforeach;?>
                        </div>
                      </div>
                    </section>
                    <footer class="modal-card-foot">
                      <button class="button" onclick="hideModal('<?php echo $order->order_code;?>')">Ocultar</button>
                    </footer>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
    <div class="column">
      <h1><strong>Total vendido</strong></h1>
    <table class="table">
        <thead>
          <tr>
            <th>Moneda</th>
            <th>Total</th>
          </tr>
        </thead>
        <tfoot>
        </tfoot>
        <tbody>
          <tr>
            <td>MXN</td>
            <td><?php echo $totals['MXN'] ?></td>
          </tr>
          <tr>
            <td>USD</td>
            <td><?php echo $totals['USD'] ?></td>
          </tr>
          <tr>
            <td>EUR</td>
            <td><?php echo $totals['EUR'] ?></td>
          </tr>
          <tr>
            <td>BOB</td>
            <td><?php echo $totals['BOB'] ?></td>
          </tr>
        </tbody>
      </table>

      <div id="chartContainer" style="height: 370px; width: 90%;"></div>
    </div>
  </div>
</div>

<script>
  showModal = (orderCode) => {
    document.getElementById("modal-" + orderCode).classList.add('is-active');
  }

  hideModal = (orderCode) => {
    document.getElementById("modal-" + orderCode).classList.remove('is-active');
  }

  applyFilter = () => {
    if (document.getElementById("datePicker").value != '') {
      window.location.href = "http://localhost:8080/orders?date=" + document.getElementById("datePicker").value;
    }
  }

  window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer", {
  animationEnabled: true,
  title: {
    text: "Cantidad vendida"
  },
  subtitles: [{
    text: ""
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
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<?=$this->endSection()?>