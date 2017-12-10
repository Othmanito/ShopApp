Vue.http.headers.common['X-CSRF-TOKEN'] = $("#token").attr("value");
new Vue({
  el :'#shop-vue',
  data :{
    isInvisible:true,
    items: [],
    pagination: {
      total: 0,
      per_page: 2,
      from: 1,
      to: 0,
      current_page: 1
    },
    offset: 4,
    formErrors:{},
    formErrorsUpdate:{},
    newItem : {'name':'','ville':''},
    fillItem : {'name':'','ville':'','id':''}

  },
  computed: {
    isActived: function() {
      return this.pagination.current_page;
    },

    pagesNumber: function() {
      if (!this.pagination.to) {
        return [];
      }
      var from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }
      var to = from + (this.offset * 2);
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }
      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    }
  },
  ready: function() {
    this.getShops(this.pagination.current_page);

    //dislike a shop and keep it hideen
    var $tab = $('#tab');
    if(localStorage.getItem("#tab")) {
        $tab.html(localStorage.getItem("#tab"));
    }
    $("#tab").on('click','.btn-danger',function(){
          $(this).closest('tr').remove();
          localStorage.setItem("#tab", $tab.html());
          setTimeout(function(){localStorage.removeItem("#tab");}, 1000*60*120);
        });
  },
  methods: {
    getShops: function(page) {
      this.$http.get('/shops?page='+page).then((response) => {
        this.$set('items', response.data.data.data);
        this.$set('pagination', response.data.pagination);
      });
    },
    changePage: function(page) {
      this.pagination.current_page = page;
      this.getShops(page);
    }
  }
});
