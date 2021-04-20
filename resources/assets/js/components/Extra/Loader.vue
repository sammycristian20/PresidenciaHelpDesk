<template>
  <div>
    <span v-if="loaderType=='clip-loader'">
      <div class="overlay-loader"></div>
      <clip-loader size="50px" class="loader-position"></clip-loader>
    </span>
    <fulfilling-bouncing-circle-spinner v-else :animation-duration="duration" :size="size" color="#1d78ff"/>
  </div>
</template>
<script>
    import ClipLoader from 'vue-spinner/src/ClipLoader.vue'
    import { FulfillingBouncingCircleSpinner } from 'epic-spinners';
    
    export default{
    props: {
      loaderType: {
        type: String,
      },
      duration: {
        type: String | Number
      },
      size: {
        type: Number,
        default: 60
      }
    },
    	components: {
            "clip-loader": ClipLoader,
            'fulfilling-bouncing-circle-spinner':FulfillingBouncingCircleSpinner,
        },
    };
</script>

<style scoped>
.overlay-loader {
  position: absolute;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: white;
  opacity: 0.8;
  filter: blur(5px);
}

.loader-position {
  position: absolute;
  left: 50%;
  right: 50%;
  bottom: 60%;
  top: 40%;
}

.fulfilling-bouncing-circle-spinner{
      margin: auto !important;
    }
</style>