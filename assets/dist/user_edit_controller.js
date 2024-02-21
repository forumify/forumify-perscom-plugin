import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  removeAssignment(event) {
    const elementToRemove = document.getElementById(`assignment-record-${event.params.toRemove}`);
    if (elementToRemove !== null) {
      elementToRemove.remove();
    }
  }
}
