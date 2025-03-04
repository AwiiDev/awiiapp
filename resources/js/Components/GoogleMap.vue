<template>
    <GmapMap
      :center="center"
      :zoom="zoom"
      style="width: 100%; height: 400px;"
      @click="handleMapClick"
    >
      <!-- Custom components or overlays here -->
      <GmapDrawingManager
        :drawingMode="drawingMode"
        @overlaycomplete="handleOverlayComplete"
      />
    </GmapMap>
  </template>
  
  <script>
  import { GmapMap, GmapDrawingManager } from 'vue3-google-map';
  
  export default {
    name: 'GoogleMap',
    props: {
      apiKey: {
        type: String,
        required: true,
      },
      center: {
        type: Object,
        default: () => ({ lat: 1.38, lng: 103.8 }), // Default center coordinates
      },
      zoom: {
        type: Number,
        default: 12,
      },
    },
    data() {
      return {
        drawingMode: null, // To control drawing mode (shape tool)
      };
    },
    methods: {
      handleMapClick(event) {
        // Handle map click events if needed
      },
      handleOverlayComplete(event) {
        // Handle overlay (polygon) completion
        const overlay = event.overlay;
        // Process the overlay (e.g., store coordinates or manipulate)
        this.drawingMode = null; // Disable drawing mode after completion
      },
    },
    components: {
      GmapMap,
      GmapDrawingManager,
    },
  };
  </script>
  