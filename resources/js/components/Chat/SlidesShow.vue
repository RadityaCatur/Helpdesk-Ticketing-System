<template>
  <div id="imagesSlideShow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="imagesSlideShowLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body pa-1">

          <div v-if="slides.length" id="chatroomImagesCarousel" class="carousel slide" data-ride="carousel">

            <ol class="carousel-indicators">
              <li
                v-for="(slide, idx) in slides"
                :key="idx"
                :data-slide-to="idx"
                :class="{active: idx===0}"
                data-target="#chatroomImagesCarousel"
              />
            </ol>

            <div class="carousel-inner" @click.right.prevent="close">
              <div
                v-for="(slide, idx) in slides"
                :key="idx"
                :class="{active: idx===0}"
                class="carousel-item"
              >
                <img class="d-block w-100" :src="'/images/'+slide.filename" alt="other slide">
              </div>
            </div>

            <a class="carousel-control-prev" href="#chatroomImagesCarousel" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true" />
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#chatroomImagesCarousel" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true" />
              <span class="sr-only">Next</span>
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'SlideShow',

  props: ['messages'],

  data() {
    return {
      slides: [],
    };
  },

  computed: {
    ...mapState({
      dialog: state => state.shared.dialog
    }),    
  },

  watch: {
    dialog(val) {
      if (val.messageIds) {
        console.log(val.messageId);
        const gotoSlide = this.slides.findIndex(el => val.messageId === el.id);
        if (gotoSlide >= 0) {
          $('#chatroomImagesCarousel').carousel(gotoSlide);
        }
      }
    },
  },

  mounted() {
    this.messages.forEach(el => {
      if (el.filename && el.filetype === 'image') {
        this.slides.push(el);
      }
    });
  },

  methods: {    
    close() {
      $('#imagesSlideShow').modal('hide');
    },
  },
};
</script>
