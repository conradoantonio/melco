<template>
  <table class="table">
    <thead>
      <tr>
        <th></th>
        <th v-for="hh in headersH" :key="hh.id">{{hh.name}}</th>
      </tr>
    </thead>
    <tbody>
      <template >
        <tr  v-for="(hv, i) in rows"  :key="hv[0].id" :id="hv[0].id">
        <th>{{ hv[0].name }}</th>
        <td :id="item.sku" v-for="item in  hv[1]" :key="item.id">
          <input :id="`${item.id}`" :name="`product_variants[${item.id}][price_sale]`" :value="item.price_sale" placeholder="precio" type='number' step="any" style="width: 80%;" class="form-control">
          <input :name="`product_variants[${item.id}][stock]`"      :value="item.stock" placeholder="stock"  type='number' step="any" style="width: 80%;" class="form-control">
          <input :name="`product_variants[${item.id}][sku]`" v-model="item.sku" type="hidden">
          <input :name="`product_variants[${item.id}][options_value]`" :value="`${JSON.stringify(item.options_value)}`" type="hidden">
        </td>
      </tr>
      </template>


    </tbody>
  </table>
</template>

<script>
export default {
  data() {
    return {
      itemsPerRow: 4,
      rows: [],
    };
  },
  props: {
    variants: Array,
    headersH: Array,
    headersV: Array,
    },

  mounted() {
    this.itemsPerRow = this.headersH.length;

    this.$root.$on('select', (option, id) => {
      this.onChangeRows(option);
     });

    this.$root.$on('remove', (option, id) => {
      this.onChangeRows(option);
    });

    this.onChangeRows('option');
  },
  computed:{
    rowCount(){
      return Math.ceil(this.variants.length / this.headersH.length);
    },
  },
  methods:{
    itemCountInRow(index){

     return this.variants.slice((index - 1) * this.headersH.length, index * this.headersH.length)
    },

    addOption(option){
      if (option.option_type_id == 1) {

        let hvrt = this.getItems(option);

        let newHvopt = [option, hvrt];

        return newHvopt;
      }
    },
    onChangeRows(option){
      let headerOpt = 0;

        this.rows.splice(0, this.rows.length);

        let rowsOpt = [];

        this.$nextTick(function () {

          this.headersV.forEach(optV => {

            let colsOpt = [];

            this.headersH.forEach(optH => {
              let retCol = this.getItems(optH, optV);

              retCol = (retCol.length > 0) ? retCol[0] : {
                                                          id: 'new'+Math.floor(Math.random() * 1000),
                                                          sku: `${optV.name}-${optH.name}`,
                                                          price_sale: 0,
                                                          stock: 0,
                                                          options_value: [
                                                            { id: optH.id, name: optH.name, option_type_id: optH.option_type_id },
                                                            { id: optV.id, name: optV.name, option_type_id: optV.option_type_id  },
                                                          ]
                                                         };

              colsOpt.push(retCol);

            });
            rowsOpt.push([optV, colsOpt]);

          });
          this.rows = rowsOpt;
        });
    },
    removeOption(option){
      this.rows.splice(0, this.rows.length);

      let rowsOpt = [];
    this.$nextTick(function () {
      this.headersV.forEach(hv => {
        rowsOpt.push(this.addOption(hv));
      });

      this.rows = rowsOpt;
    });


    },
    getItems(optionH, optionV){

      return this.variants.filter(variant => {
          if (variant.options_value.length == 0) return false;

            if ((variant.options_value[0].name == optionH.name) && (variant.options_value[1].name == optionV.name)
               ||
               ((variant.options_value[1].name == optionH.name) && (variant.options_value[0].name == optionV.name))){
              return true;
            }
        });
    },
    getHeaderV(i){

      let str = this.variants[(i - 1) * this.headersH.length].sku

      return str.substring(0, str.indexOf("-"));
    }
  },
  watch: {
    headersV(newValue, oldValue) {
      if(newValue.length > oldValue.length){

      }
    }
  },

};
</script>
