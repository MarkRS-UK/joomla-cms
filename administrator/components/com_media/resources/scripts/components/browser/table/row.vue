<template>
  <tr
    class="media-browser-item"
    :class="{selected}"
    @dblclick.stop.prevent="onDblClick()"
    @click="onClick"
  >
    <td
      v-if="item.mime_type === 'image/svg+xml' && getURL()"
    >
      <img
        :src="getURL()"
        :width="item.width"
        :height="item.height"
        alt=""
        style="width:100%;height:auto"
        @load="setSize"
      >
    </td>
    <td
      v-else
      class="type"
      :data-type="item.extension"
    />
    <th
      scope="row"
      class="name"
    >
      {{ item.name }}
    </th>
    <td class="size">
      {{ size }}<span v-if="size !== ''">KB</span>
    </td>
    <td class="dimension">
      {{ dimension }}
    </td>
    <td class="created">
      {{ item.create_date_formatted }}
    </td>
    <td class="modified">
      {{ item.modified_date_formatted }}
    </td>
  </tr>
</template>

<script>
import api from '../../../app/Api.es6';
import * as types from '../../../store/mutation-types.es6';
import navigable from '../../../mixins/navigable.es6';
import onItemClick from '../utils/utils.es6';

export default {
  name: 'MediaBrowserItemRow',
  mixins: [navigable],
  props: {
    item: {
      type: Object,
      default: () => {},
    },
    localItems: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    /* The dimension of a file */
    dimension() {
      if (!this.item.width) {
        return '';
      }
      return `${this.item.width}px * ${this.item.height}px`;
    },
    isDir() {
      return (this.item.type === 'dir');
    },
    /* The size of a file in KB */
    size() {
      if (!this.item.size) {
        return '';
      }
      return `${(this.item.size / 1024).toFixed(2)}`;
    },
    selected() {
      return !!this.isSelected();
    },
  },

  methods: {
    getURL() {
      if (!this.item.thumb_path) {
        return '';
      }

      return this.item.thumb_path.split(Joomla.getOptions('system.paths').rootFull).length > 1
        ? `${this.item.thumb_path}?${this.item.modified_date ? new Date(this.item.modified_date).valueOf() : api.mediaVersion}`
        : `${this.item.thumb_path}`;
    },
    width() {
      return this.item.naturalWidth ? this.item.naturalWidth : 300;
    },
    height() {
      return this.item.naturalHeight ? this.item.naturalHeight : 150;
    },
    setSize(event) {
      if (this.item.mime_type === 'image/svg+xml') {
        const image = event.target;
        // Update the item properties
        this.$store.dispatch('updateItemProperties', { item: this.item, width: image.naturalWidth ? image.naturalWidth : 300, height: image.naturalHeight ? image.naturalHeight : 150 });
        // @TODO Remove the fallback size (300x150) when https://bugzilla.mozilla.org/show_bug.cgi?id=1328124 is fixed
        // Also https://github.com/whatwg/html/issues/3510
      }
    },
    /* Handle the on row double click event */
    onDblClick() {
      if (this.isDir) {
        this.navigateTo(this.item.path);
        return;
      }

      // @todo remove the hardcoded extensions here
      const extensionWithPreview = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'mp4', 'mp3', 'pdf'];

      // Show preview
      if (this.item.extension
        && extensionWithPreview.includes(this.item.extension.toLowerCase())) {
        this.$store.commit(types.SHOW_PREVIEW_MODAL);
        this.$store.dispatch('getFullContents', this.item);
      }
    },

    /**
     * Whether or not the item is currently selected
     * @returns {boolean}
     */
    isSelected() {
      return this.$store.state.selectedItems.some((selected) => selected.path === this.item.path);
    },

    /**
     * Handle the click event
     * @param event
     */
    onClick(event) {
      return onItemClick(event, this);
    },

  },
};
</script>
