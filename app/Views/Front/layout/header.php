<section class="hero is-info">
  <div class="hero-body">
    <p class="title">
      Listado de productos
    </p>
  </div>

  <div class="hero-foot">
    <nav class="tabs is-boxed is-fullwidth">
      <div class="container">
        <ul>
          <li class="<?=service('request')->uri->getPath() == '/' ? 'is-active' : '' ?>">
            <a href="<?=base_url(route_to(''))?>">Productos mas vendidos</a>
          </li>
          <li class="<?=service('request')->uri->getPath() == 'orders' ? 'is-active' : '' ?>">
            <a href="<?=base_url(route_to('orders'))?>">Listado de ordenes</a>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</section>