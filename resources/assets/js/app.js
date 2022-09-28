
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('variant-price', require('./components/variants-price.vue'));

//Vue.component('vue-multiselect', window.VueMultiselect.default);

Vue.component('tag-multiselect', require('./components/tags-multiselect.vue'));
//Vue.component('vmultiselect', require('./components/multiselect.vue'));
/*
import axios from "axios";

window.testVue = new Vue({
  components: {
    Multiselect: window.VueMultiselect.default
  },
  data: {
    value: [{ language: 'JavaScript', library: 'Vue-Multiselect' }],
    options: [
      { language: 'JavaScript', library: 'Vue.js' },
      { language: 'JavaScript', library: 'Vue-Multiselect' },
      { language: 'JavaScript', library: 'Vuelidate' }
    ],
    //type: 0,
  },
  props: {
    //type: Number,
  },
  mounted: function() {
    console.info("new Vue type => ", this.type);

    axios.get("/api/products/options?type=" + this.type).then(response => {
      //this.options = response.data;
    });
  },
  methods: {
    customLabel(option) {
      return `${option.library} - ${option.language}`
    },
    addTag(newTag) {
      const tag = {
        name: newTag,
        code: newTag.substring(0, 2) + Math.floor(Math.random() * 10000000)
      };
      this.options.push(tag);
      this.value.push(tag);
    },
    updateSelected(newSelected) {
      console.info('updateSelected => ', newSelected);
      this.selected = newSelected
    },
    asyncFind(query) {
      if (query.length > 3) {
        this.isLoading = true;
        axios.get("/api/tags/" + this.tagGroup + "/" + query).then(response => {
          this.tags = response.data.results.map(a => {
            return { name: a.name.en };
          });
        });
      }
    }
  }
}).$mount('#testapp');
 */

window.appVue = new Vue({
  el: '#app',
  data: {
    vsizes: [],
    vqualities: [],
    vsizesvalues: [{"name":"G","text":"G","id":6,"position":3,"option_type_id":2}],
  },
  props: {
  },
  methods: {
    usizes(value){
      this.vsizes = value;
    },
    uqualities(value){
      this.vqualities = value;
    }
  }
});
