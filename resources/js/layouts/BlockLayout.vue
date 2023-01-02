<template>
  <section>

    <header>
      <h2>
        <slot name="title"></slot>
      </h2>
      <div>
        <slot name="header"></slot>
      </div>
    </header>

    <main>
      <slot></slot>
    </main>

    <footer>
      <slot name="footer"></slot>
    </footer>

  </section>
</template>

<script setup>

</script>
