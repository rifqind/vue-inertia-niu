<template>
    <div class="text-center overlay" id="spinner-border">
        <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</template>
<style scoped>
.overlay {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    /* Center horizontally */
    align-items: center;
    transition: opacity 0.3s ease;
}
</style>