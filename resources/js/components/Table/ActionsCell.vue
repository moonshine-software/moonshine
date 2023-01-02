<script setup lang="ts">
import EditButton from "../UI/EditButton.vue";
import DeleteButton from "../UI/DeleteButton.vue";
import ViewButton from "../UI/ViewButton.vue";
import DeleteConfirmationModal from "../UI/Modals/DeleteConfirmationModal.vue";
import {PrimaryKey} from "../../entites/primary_key";
import {useResourceStore} from "../../store/resource";

const props = defineProps<{ resourceId: PrimaryKey }>()

const resource = useResourceStore(),
    uriKey = resource.uri,
    onConfirm = () => resource.delete(props.resourceId)

</script>

<template>
  <td class="space-x-2">

    <router-link :to="{name: 'show', params: {resourceName: uriKey, resourceId}}" tabindex="-1" class="inline-block">
      <ViewButton/>
    </router-link>

    <router-link :to="{name: 'edit', params: {resourceName: uriKey, resourceId}}" tabindex="-1" class="inline-block">
      <EditButton/>
    </router-link>

    <DeleteConfirmationModal :on-confirm="onConfirm" close-on-delete class="inline-block">
      <template #trigger>
        <DeleteButton class="inline-block"/>
      </template>
    </DeleteConfirmationModal>

  </td>
</template>
