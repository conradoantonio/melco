<template>
  <multiselect
    v-model="value"
    tag-placeholder="Agregar como nuevo tag"
    placeholder="Buscar o agregar tag"
    select-label="Presionar enter para seleccionar"
    deselect-label="Presionar enter para remover"
    selected-label="Seleccionado"
    label="name"
    track-by="id"
    :selected="selected"
    :loading="false"
    :options="options"
    :multiple="true"
    :taggable="true"
    :closeOnSelect="false"
    @input="updateSelected"
    @select="selectedItem"
    @remove="removeItem"
    @tag="addTag"
    @search-change="asyncFind"
  ></multiselect>
</template>

<script>
//import Multiselect from "vue-multiselect";
import axios from "axios";

export default {
  components: {
    Multiselect: window.VueMultiselect.default
  },
  data() {
    return {
      value: [],
      selected: [],
      options: []
      //type: 0,
    };
  },
  props: {
    type: Number,
    //selected: this.value,
    taggingSelected: Array,
    product: Number
  },
  mounted: function() {
    axios
      .get(`/api/products/options?type=${this.type}&product_id=${this.product}`)
      .then(response => {
        this.options = response.data;

        this.options.forEach(element => {
          if (element.selected) {
            this.value.push(element);
            this.selected.push(element);
            this.$emit("update", this.selected);
          }
        });
      });
  },
  methods: {
    addTag(newTag) {
      const tag = {
        name: newTag,
        id: newTag,
        code: newTag.substring(0, 2) + Math.floor(Math.random() * 10000000),
        option_type_id: this.type,
      };
      this.options.push(tag);
      this.value.push(tag);
      this.selected.push(tag);

      this.$root.$emit('select', tag, tag.code);
    },
    updateSelected(newSelected) {

      this.selected = newSelected;

      this.$emit("update", this.selected);
    },
    removeItem(removedOption, id){

      this.$root.$emit('remove', removedOption, id);
    },
    selectedItem(selectedOption, id){

      this.$root.$emit('select', selectedOption, id);
    },
    syncValue(value) {
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
};
</script>
