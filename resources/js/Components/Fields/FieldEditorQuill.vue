<template>
  <div class="form-group" :class="classObject">

    <!-- Label -->
    <label :for="id" class="form-label">
      {{ label }} <strong v-if="required">*</strong>
    </label>

    <!-- Editor contenedor -->
    <div
      :id="id"
      ref="editorRef"
      class="quill-editor"
      :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
      :style="computedStyle"
    ></div>

    <!-- Error -->
    <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback d-block">
      {{ formError || validationMessage }}
    </div>

  </div>
</template>

 
<script>
import { onMounted, watch, ref, nextTick } from "vue";
import Quill from "quill";


import "quill/dist/quill.snow.css";

export default {
  props: {
    id: String,
    label: String,
    modelValue: { type: String, default: "" },
    placeholder: String,
    required: Boolean,
    showValidation: Boolean,
    formError: String,
    height: [Number, String],
    validateFunction: Function,
    classObject: String,
    readonly: Boolean,
    toolbar: { type: [String, Array, Boolean], default: "full" },
    uploadUrl: String
  },

  emits: ["update:modelValue", "blur"],

  setup(props, { emit }) {
    const editorRef = ref(null);
    let quill = null;

    const TOOLBARS = {
      full: [
        [{ header: [1, 2, 3, false] }],
        ["bold", "italic", "underline", "strike"],
        [{ list: "ordered" }, { list: "bullet" }],
        [{ align: [] }],
        ["blockquote", "code-block"],
        ["link", "image"]
      ],
      standard: [
        [{ header: [1, 2, 3, false] }],
        ["bold", "italic", "underline"],
        [{ list: "ordered" }, { list: "bullet" }],
        ["link"]
      ],
      basic: [["bold", "italic"], [{ list: "ordered" }, { list: "bullet" }]]
    };

    function imageHandler() {
      if (!props.uploadUrl) return;

      const input = document.createElement("input");
      input.type = "file";
      input.accept = "image/*";

      input.onchange = async () => {
        const file = input.files?.[0];
        if (!file) return;

        const fd = new FormData();
        fd.append("image", file);

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

        const res = await fetch(props.uploadUrl, {
          method: "POST",
          headers: csrf ? { "X-CSRF-TOKEN": csrf } : {},
          body: fd
        });

        const data = await res.json();

        if (data.url) {
          const range = quill.getSelection(true);
          quill.insertEmbed(range.index, "image", data.url);
        }
      };

      input.click();
    }

    // LIMPIEZA LIGERA (NO tocamos <p><br></p>)
    function cleanHTML(html) {
      return html
        .replace(/<p>\s*<\/p>/g, "") // párrafos totalmente vacíos
        .trim();
    }

    onMounted(async () => {
      await nextTick();

      const toolbarConfig =
        props.toolbar === false
          ? false
          : Array.isArray(props.toolbar)
          ? props.toolbar
          : TOOLBARS[props.toolbar] ?? TOOLBARS.standard;

      quill = new Quill(editorRef.value, {
        theme: "snow",
        placeholder: props.placeholder,
        readOnly: props.readonly
      });

      // Altura opcional
      if (props.height) {
        const h =
          typeof props.height === "number" ? `${props.height}px` : props.height;
        editorRef.value.querySelector(".ql-editor").style.minHeight = h;
      }

      // SOLO inicializar contenido antes de registrar text-change
      quill.root.innerHTML = props.modelValue || "";

      // Emitir cambios
      quill.on("text-change", () => {
        const clean = cleanHTML(quill.root.innerHTML);
        emit("update:modelValue", clean);
      });

      quill.root.addEventListener("blur", () => emit("blur"));
    });

    // NO usamos dangerouslyPasteHTML aquí porque rompe el cursor
    watch(
      () => props.modelValue,
      (v) => {
        if (!quill) return;

        const clean = cleanHTML(v || "");
        const current = quill.root.innerHTML.trim();

        if (clean !== current) {
          quill.root.innerHTML = clean; // NO rompe cursor
        }
      }
    );

    return { editorRef };
  }
};
</script>



<style scoped>
.quill-editor {
  border: 1px solid #ced4da;
  border-radius: 0.375rem;
  background: #fff;
}

.quill-editor .ql-container {
  border: none;
}

.quill-editor.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  font-size: 0.85rem;
}

.quill-editor .ql-editor {
  min-height: 150px;
  padding: 0.75rem 1rem;
}
</style>
