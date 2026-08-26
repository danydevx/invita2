<template>
  <section class="vcard-section vcard-contact-form">
    <h2 class="vcard-section__title">Contacto</h2>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" class="form-control" placeholder="Tu nombre">
        </div>
        <div class="mb-3">
          <label class="form-label">Correo</label>
          <input type="email" class="form-control" placeholder="correo@ejemplo.com">
        </div>
        <div class="mb-3">
          <label class="form-label">Mensaje</label>
          <textarea class="form-control" rows="4" placeholder="Escribe tu mensaje"></textarea>
        </div>
        <button type="button" class="btn btn-primary w-100">Enviar mensaje</button>
      </div>
    </div>
  </section>
</template>
