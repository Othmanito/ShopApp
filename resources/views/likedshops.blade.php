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
  <h2>List of Prefered Shops</h2>
  <div class="row">

    <div class="table-responsive">
      <center>
      <table class="table table-bordered table-hover table-condensed">
        <tr class="active">
          <th>Shop Name</th>
          <th>Shop City</th>
          <th>Distance</th>
          <th>Actions</th>
        </tr>
        @foreach($liked as $item)
        <tr >
          <td>{{ $item->name }}</td>
          <td>{{ $item->ville }}</td>
          <td>{{ $item->distance }} metres</td></div>
            </div>
          </td>
          <td>
            <form action="{{ route('removeLike',[$item->id]) }}" method="post">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button class="edit-modal btn btn-danger" type="submit" >
              <span class="glyphicon glyphicon-trash"></span> Remove
            </button>
          </form>
          </td>
        </tr>
        @endforeach
      </table>
    </div>
  </div>
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
