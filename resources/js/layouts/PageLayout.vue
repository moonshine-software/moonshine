<template>
  <section class="ms-page">

    <header>
      <h1 class="text-4xl">
        <slot name="title"></slot>
      </h1>

      <section>
        <slot name="header"></slot>
      </section>
    </header>

    <main class="space-y-6 mb-auto">
      <slot></slot>
    </main>

    <footer>
      <slot name="footer">
        <span class="text-secondary-400 dark:text-secondary-500 text-xs">
          Moonshine. 2022
        </span>
      </slot>
    </footer>

  </section>
</template>

<style>
.ms-page {
  @apply space-y-6 px-4 md:px-8 py-6 flex flex-col justify-between flex-grow w-full;
  @apply overflow-y-scroll h-screen;
}
</style>
