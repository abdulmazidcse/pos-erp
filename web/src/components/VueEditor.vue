<template>
  <div class="quillWrapper">
    <slot name="toolbar"></slot>
    <div :id="id" ref="quillContainer"></div>
    <input
      v-if="useCustomImageHandler"
      id="file-upload"
      ref="fileInput"
      type="file"
      accept="image/*"
      style="display:none;"
      @change="emitImageInfo($event)"
    />
  </div>
</template>

<script>
import Quill from 'quill';
import defaultToolbar from '@/helpers/default-toolbar';
import oldApi from '@/helpers/old-api';
import mergeDeep from '@/helpers/merge-deep';
import MarkdownShortcuts from '@/helpers/markdown-shortcuts';

export default {
  name: 'VueEditor',
  emits: [
    'ready',
    'editor-change',
    'focus',
    'selection-change',
    'text-change',
    'blur',
    'input',
    'image-removed',
    'image-added',
    'update:modelValue',
  ],
  mixins: [oldApi],
  props: {
    id: {
      type: String,
      default: 'quill-container',
    },
    placeholder: {
      type: String,
      default: '',
    },
    modelValue: {
      type: String,
      default: '',
    },
    disabled: {
      type: Boolean,
    },
    editorToolbar: {
      type: Array,
      default: () => [],
    },
    editorOptions: {
      type: Object,
      required: false,
      default: () => ({}),
    },
    useCustomImageHandler: {
      type: Boolean,
      default: false,
    },
    useMarkdownShortcuts: {
      type: Boolean,
      default: false,
    },
  },

  data: () => ({
    quill: null,
  }),

  watch: {
    modelValue(val) {
      if (val != this.quill.root.innerHTML && !this.quill.hasFocus()) {
        this.quill.root.innerHTML = val;
      }
    },
    disabled(status) {
      this.quill.enable(!status);
    },
  },

  mounted() {
    this.registerCustomModules(Quill);
    this.registerPrototypes();
    this.initializeEditor();
  },

  beforeUnmount() {
    this.quill = null;
    delete this.quill;
  },

  methods: {
    initializeEditor() {
      this.setupQuillEditor();
      this.checkForCustomImageHandler();
      this.handleInitialContent();
      this.registerEditorEventListeners();
      this.$emit('ready', this.quill);
    },

    setupQuillEditor() {
      let editorConfig = {
        debug: false,
        modules: this.setModules(),
        theme: 'snow',
        placeholder: this.placeholder ? this.placeholder : '',
        readOnly: this.disabled ? this.disabled : false,
      };

      this.prepareEditorConfig(editorConfig);
      this.quill = new Quill(this.$refs.quillContainer, editorConfig);
    },

    setModules() {
      let modules = {
        toolbar: this.editorToolbar.length ? this.editorToolbar : defaultToolbar,
      };
      if (this.useMarkdownShortcuts) {
        Quill.register('modules/markdownShortcuts', MarkdownShortcuts, true);
        modules['markdownShortcuts'] = {};
      }
      return modules;
    },

    prepareEditorConfig(editorConfig) {
      if (Object.keys(this.editorOptions).length > 0 && this.editorOptions.constructor === Object) {
        if (
          this.editorOptions.modules &&
          typeof this.editorOptions.modules.toolbar !== 'undefined'
        ) {
          // We don't want to merge default toolbar with provided toolbar.
          delete editorConfig.modules.toolbar;
        }

        mergeDeep(editorConfig, this.editorOptions);
      }
    },

    registerPrototypes() {
      Quill.prototype.getHTML = function() {
        return this.container.querySelector('.ql-editor').innerHTML;
      };
      Quill.prototype.getWordCount = function() {
        return this.container.querySelector('.ql-editor').innerText.length;
      };
    },

    registerEditorEventListeners() {
      this.quill.on('text-change', this.handleTextChange);
      this.quill.on('selection-change', this.handleSelectionChange);
      this.listenForEditorEvent('text-change');
      this.listenForEditorEvent('selection-change');
      this.listenForEditorEvent('editor-change');
    },

    listenForEditorEvent(type) {
      this.quill.on(type, (...args) => {
        this.$emit(type, ...args);
      });
    },

    handleInitialContent() {
      if (this.modelValue) this.quill.root.innerHTML = this.modelValue; // Set initial editor content
    },

    handleSelectionChange(range, oldRange) {
      if (!range && oldRange) this.$emit('blur', this.quill);
      else if (range && !oldRange) this.$emit('focus', this.quill);
    },

    handleTextChange(delta, oldContents) {
      let editorContent = this.quill.getHTML() === '<p><br></p>' ? '' : this.quill.getHTML();
      this.$emit('update:modelValue', editorContent);

      if (this.useCustomImageHandler) this.handleImageRemoved(delta, oldContents);
    },

    handleImageRemoved(delta, oldContents) {
      const currrentContents = this.quill.getContents();
      const deletedContents = currrentContents.diff(oldContents);
      const operations = deletedContents.ops;

      operations.map(operation => {
        // eslint-disable-next-line no-prototype-builtins
        if (operation.insert && operation.insert.hasOwnProperty('image')) {
          const { image } = operation.insert;
          this.$emit('image-removed', image);
        }
      });
    },
    checkForCustomImageHandler() {
      this.useCustomImageHandler === true ? this.setupCustomImageHandler() : '';
    },

    setupCustomImageHandler() {
      let toolbar = this.quill.getModule('toolbar');
      toolbar.addHandler('image', this.customImageHandler);
    },

    customImageHandler() {
      this.$refs.fileInput.click();
    },

    emitImageInfo($event) {
      const resetUploader = function() {
        var uploader = document.getElementById('file-upload');
        uploader.value = '';
      };
      let file = $event.target.files[0];
      let Editor = this.quill;
      let range = Editor.getSelection();
      let cursorLocation = range.index;
      this.$emit('image-added', file, Editor, cursorLocation, resetUploader);
    },
  },
};
</script>

<style src="quill/dist/quill.snow.css"></style>
<style src="../assets/vue3-editor.scss" lang="scss"></style>