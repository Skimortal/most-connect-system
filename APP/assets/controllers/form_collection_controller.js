// assets/controllers/form_collection_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['list', 'prototype'];
    static values = { index: Number };

    connect() {
        if (!this.hasIndexValue) {
            this.indexValue = this.listTarget.querySelectorAll('[data-collection-item]').length;
        }
        this.renumber();
    }

    add(e) {
        e.preventDefault();
        // 1) Template-Inhalt (DocumentFragment) klonen …
        const frag = this.prototypeTarget.content.cloneNode(true);
        // 2) Platzhalter __name__ im gesamten Fragment ersetzen
        this._replaceInFragment(frag, '__name__', String(this.indexValue++));
        // 3) ins <tbody> hängen
        console.log(frag);
        this.listTarget.appendChild(frag);
        this.renumber();
    }

    remove(e) {
        e.preventDefault();
        const row = e.currentTarget.closest('[data-collection-item]');
        if (row) row.remove();
        this.renumber();
    }

    renumber() {
        this.listTarget.querySelectorAll('[data-collection-item] [data-collection-counter]')
            .forEach((el, i) => el.textContent = String(i + 1));
    }

    _replaceInFragment(fragment, search, replace) {
        // ersetzt in allen Attributen und Textknoten __name__
        const treeWalker = document.createTreeWalker(fragment, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT);
        const nodes = [];
        while (treeWalker.nextNode()) nodes.push(treeWalker.currentNode);

        nodes.forEach(node => {
            if (node.nodeType === Node.TEXT_NODE) {
                node.nodeValue = node.nodeValue.replaceAll(search, replace);
            } else if (node.nodeType === Node.ELEMENT_NODE) {
                for (const attr of Array.from(node.attributes)) {
                    if (attr.value.includes(search)) {
                        node.setAttribute(attr.name, attr.value.replaceAll(search, replace));
                    }
                }
            }
        });
    }
}
