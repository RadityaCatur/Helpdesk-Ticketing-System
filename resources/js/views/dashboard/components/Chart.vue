<script>
import { Bar } from 'vue-chartjs';

export default {
  extends: Bar,
  props: ['open', 'onProgress', 'closed', 'options', 'labels'],
  watch: {
    open: {
      handler() {
        this._data._chart.destroy();
        this.lineRenderChart();
      },
      deep: true,
    },
    onProgress: {
      handler() {
        this._data._chart.destroy();
        this.lineRenderChart();
      },
      deep: true,
    },
    closed: {
      handler() {
        this._data._chart.destroy();
        this.lineRenderChart();
      },
      deep: true,
    },
  },
  mounted() {
    this.lineRenderChart();
  },
  methods: {
    lineRenderChart() {
      this.renderChart({
        labels: this.labels,
        datasets: [
          {
            label: 'Open Tiket',
            data: this.open,
            fill: false,
            borderColor: '#f17e5d',
            backgroundColor: '#f17e5d',
            borderWidth: 1,
          },
          {
            label: 'OnProgress Tiket',
            data: this.onProgress,
            fill: false,
            borderColor: '#fcc468',
            backgroundColor: '#fcc468',
            borderWidth: 1,
          },
          {
            label: 'Closed Tiket',
            data: this.closed,
            fill: false,
            borderColor: '#6bd098',
            backgroundColor: '#6bd098',
            borderWidth: 1,
          },
        ],
      }, this.options);
    },
  },
};
</script>

