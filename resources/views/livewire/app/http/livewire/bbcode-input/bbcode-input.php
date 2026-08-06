<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use Livewire\Component;

new class extends Component
{
    public string $label = '';

    public string $name = '';

    public bool $isPreviewEnabled = false;

    public bool $isRequired = false;

    public string $contentBbcode = '';

    public string $contentHtml = '';

    final public function mount(string $name, string $label, bool $required = false, ?string $content = null): void
    {
        $this->name = $name;
        $this->label = $label;
        $this->isRequired = $required;
        $this->contentBbcode = $content ?? old($name) ?? '';
    }

    final public function render(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return $this->view([
            'contentBbcode' => $this->contentBbcode,
            'label'         => $this->label,
        ]);
    }
};
