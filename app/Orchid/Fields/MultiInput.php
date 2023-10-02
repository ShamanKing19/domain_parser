<?php

declare(strict_types=1);

namespace App\Orchid\Fields;

use Orchid\Screen\Concerns\Multipliable;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

/**
 * Class Input.
 *
 * @method MultiInput accept($value = true)
 * @method MultiInput accesskey($value = true)
 * @method MultiInput autocomplete($value = true)
 * @method MultiInput autofocus($value = true)
 * @method MultiInput checked($value = true)
 * @method MultiInput disabled($value = true)
 * @method MultiInput form($value = true)
 * @method MultiInput formaction($value = true)
 * @method MultiInput formenctype($value = true)
 * @method MultiInput formmethod($value = true)
 * @method MultiInput formnovalidate($value = true)
 * @method MultiInput formtarget($value = true)
 * @method MultiInput max(int $value)
 * @method MultiInput maxlength(int $value)
 * @method MultiInput min(int $value)
 * @method MultiInput minlength(int $value)
 * @method MultiInput name(string $value = null)
 * @method MultiInput pattern($value = true)
 * @method MultiInput placeholder(string $value = null)
 * @method MultiInput readonly($value = true)
 * @method MultiInput required(bool $value = true)
 * @method MultiInput size($value = true)
 * @method MultiInput src($value = true)
 * @method MultiInput step($value = true)
 * @method MultiInput tabindex($value = true)
 * @method MultiInput type($value = true)
 * @method MultiInput value($value = true)
 * @method MultiInput help(string $value = null)
 * @method MultiInput popover(string $value = null)
 * @method MultiInput mask($value = true)
 * @method MultiInput title(string $value = null)
 * @method MultiInput columns(array $value = []) Values
 */
class MultiInput extends Field
{
    use Multipliable;

    /**
     * @var string
     */
    protected $view = 'admin.fields.multyinput';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'class'    => 'form-control multiple',
        'datalist' => [],
        'columns' => [],
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'accept',
        'accesskey',
        'autocomplete',
        'autofocus',
        'checked',
        'disabled',
        'form',
        'formaction',
        'formenctype',
        'formmethod',
        'formnovalidate',
        'formtarget',
        'list',
        'max',
        'maxlength',
        'min',
        'minlength',
        'name',
        'pattern',
        'placeholder',
        'readonly',
        'required',
        'size',
        'src',
        'step',
        'tabindex',
        'type',
        'value',
        'mask',
    ];

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->addBeforeRender(function () {
            $mask = $this->get('mask');

            if (is_array($mask)) {
                $this->set('mask', json_encode($mask));
            }
        });
    }

    /**
     * @param array $datalist
     *
     * @return Input
     */
    public function datalist(array $datalist = []): self
    {
        if (empty($datalist)) {
            return $this;
        }

        $this->set('datalist', $datalist);

        return $this->addBeforeRender(function () {
            $this->set('list', 'datalist-'.$this->get('name'));
        });
    }
}
