export class LeadForm {
  constructor(form, options = {}) {
    this.form = form;
    this.endpoint = form.action;
    this.messageBox = form.querySelector('.js-form-feedback');
    this.submitBtn = form.querySelector('[type="submit"]');

    this.options = {
      loadingText: 'Sending...',
      successMessage: 'Your submission has been received.',
      errorMessage: 'Something went wrong. Please try again.',
      ...options
    };

    this.originalBtnText = this.submitBtn?.value || 'Submit';

    this.init();
  }

  init() {
    this.setLoadedTimestamp();
    this.form.addEventListener('submit', this.handleSubmit.bind(this));
  }

  setLoadedTimestamp() {
    const input = this.form.querySelector('[name="form_loaded_at"]');
    if (input) {
      input.value = Date.now().toString();
    }
  }

  setState(state) {
    if (!this.submitBtn) return;

    switch (state) {
      case 'loading':
        this.submitBtn.disabled = true;
        this.submitBtn.value = this.options.loadingText;
        break;

      case 'idle':
        this.submitBtn.disabled = false;
        this.submitBtn.value = this.originalBtnText;
        break;
    }
  }

  showMessage(message, type = 'success') {
    if (!this.messageBox) return;

    this.messageBox.textContent = message;
    this.messageBox.dataset.state = type;
  }

  async handleSubmit(e) {
    e.preventDefault();

    this.clearMessage();

    if (!this.form.checkValidity()) {
      this.form.reportValidity();
      return;
    }

    this.setState('loading');

    try {
      const response = await fetch(this.endpoint, {
        method: 'POST',
        body: new FormData(this.form),
        headers: {
          'Accept': 'application/json'
        }
      });

      const data = await this.safeParse(response);

      if (!response.ok || !data.ok) {
        throw new Error(data.message || this.options.errorMessage);
      }

      this.onSuccess(data);

    } catch (error) {
      this.onError(error);
    } finally {
      this.setState('idle');
    }
  }

  async safeParse(response) {
    try {
      return await response.json();
    } catch {
      return {};
    }
  }

  onSuccess(data) {
    this.showMessage(
      data.message || this.options.successMessage,
      'success'
    );

    this.form.reset();
    this.setLoadedTimestamp();

    // Hook pour extensions
    this.emit('leadform:success', data);
  }

  onError(error) {
    this.showMessage(
      error.message || this.options.errorMessage,
      'error'
    );

    this.emit('leadform:error', error);
  }

  clearMessage() {
    if (!this.messageBox) return;
    this.messageBox.textContent = '';
    this.messageBox.dataset.state = '';
  }

  emit(eventName, detail) {
    this.form.dispatchEvent(new CustomEvent(eventName, { detail }));
  }
}

export function initLeadForms(selector = '.js-lead-form', options = {}) {
  const forms = document.querySelectorAll(selector);

  return Array.from(forms).map(form => new LeadForm(form, options));
}