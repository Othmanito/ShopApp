@extends('layouts.application')
@section('content')
<center>
  <div class="form-group row add">
    <div class="col-md-6">

      <a href="/Listshops"><h4>My Nearby Shops</h3></a>

    </div>
    <div class="col-md-6">
      <a href="/Likedshops"><h4>My Prefered Shops</h3></a>
    </div>
  </div>

  <div class="row">

<h2>List of nearby Shops</h2>
    <div class="table-responsive">

      <table id="tab" class="table table-bordered table-hover table-condensed">

       <tr class="active">
          <th><center> Shop Name</th>
          <th><center> Shop City</th>
          <th><center> Distance</th>
          <th><center> Actions</th>
        </tr>

        <tr v-for="item in items" >
          <td><center> @{{ item.name }}</td>
          <td><center> @{{ item.ville }}</td>
          <td><center>@{{ item.distance }} metres</td>

          <td>
            <div class="col-md-2"></div><div class="col-md-2"></div>

            <!-- Bouton like -->
            <div class="col-lg-2">

             <form action="/makeLike/@{{ item.id }}" method="post">
               <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button class=" btn btn-success" type="submit" >
              <span class="glyphicon glyphicon-thumbs-up" ></span> Like
            </button>
          </form>
        </div>

         <!-- Bouton Dislike -->
        <div class="col-lg-2">

            <button class="btn btn-danger"  >
              <span class="glyphicon glyphicon-thumbs-down"></span> Dislike
            </button>

        </div>
          </td>
        </tr>
        </div>
      </table>

  </div></div>
  <!-- Systeme de pagination  -->
  <nav>
    <ul class="pagination">
      <li v-if="pagination.current_page > 1">
        <a href="#" aria-label="Previous" @click.prevent="changePage(pagination.current_page - 1)">
          <span aria-hidden="true">«</span>
        </a>
      </li>
      <li v-for="page in pagesNumber" v-bind:class="[ page == isActived ? 'active' : '']">
        <a href="#" @click.prevent="changePage(page)">
          @{{ page }}
        </a>
      </li>
      <li v-if="pagination.current_page < pagination.last_page">
        <a href="#" aria-label="Next" @click.prevent="changePage(pagination.current_page + 1)">
          <span aria-hidden="true">»</span>
        </a>
      </li>
    </ul>
  </nav>
@stop
<!-- Fin du Systeme de pagination  -->
