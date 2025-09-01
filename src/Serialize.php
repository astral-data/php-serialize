<?php

namespace Astral\Serialize;

use Astral\Serialize\OpenApi\Handler\Config;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Support\Factories\ContextFactory;
use JsonSerializable;

/**
 * @method void withResponses(array $responses) static
 * @see SerializeContext::withResponses()
 * @method void setCode(string|int $code) static
 * @see SerializeContext::setCode()
 * @method void setMessage(string $message) static
 * @see SerializeContext::setMessage()
 */
abstract class Serialize implements JsonSerializable
{
    private ?SerializeContext $_context = null;

    public function getContext(): ?SerializeContext
    {
        return $this->_context;
    }

    public function setContext(SerializeContext $context): static
    {
        $this->_context = $context;
        return  $this;
    }

    public static function setGroups(array|string $groups): SerializeContext
    {
        /** @var SerializeContext<static> $serializeContext */
        $serializeContext = ContextFactory::build(static::class);
        return $serializeContext->setGroups((array)$groups);
    }

    public function withGroups(array|string $groups): static
    {
        $this->getContext()?->setGroups((array)$groups);
        return $this;
    }

    public function toJsonString(): bool|string
    {
        return json_encode($this);
    }

    public function withoutResponseToJsonString(): string
    {
        return json_encode($this->toArray());
    }

    public function toArray(): array
    {
        if ($this->getContext() === null) {
            $this->setContext(ContextFactory::build(static::class));
        }

        return $this->getContext()->toArray($this);
    }

    public static function from(...$payload): static
    {
        $serializeContext = ContextFactory::build(static::class);

        /** @var static $instance */
        $instance = $serializeContext->from(...$payload);
        $instance->setContext($serializeContext);

        return $instance;
    }

    public static function faker(): static
    {
        $serializeContext = ContextFactory::build(static::class);

        /** @var static $instance */
        $instance = $serializeContext->faker();
        $instance->setContext($serializeContext);

        return $instance;
    }

    public function __debugInfo()
    {
        $res             = get_object_vars($this);
        $res['_context'] = null;

        return $res;
    }

    public function jsonSerialize(): array
    {
        $baseResponses     = Config::get('response', []);
        $customerResponses = $this->getContext()?->getResponses() ?? [];
        $responses         = array_merge($baseResponses, $customerResponses);

        if ($responses) {
            $resultData = [];
            foreach ($responses as $field => $item) {
                if ($item === 'T') {
                    $resultData[$field] = $this->toArray();
                } else {
                    $resultData[$field] = $item['value'] ?? ($item['example'] ?? '');
                }
            }
            return $resultData;
        }

        return $this->toArray();
    }

    public function __call(string $name, array $arguments)
    {
        if ($this->getContext() === null) {
            $this->setContext(ContextFactory::build(static::class));
        }

        $this->getContext()->{$name}(...$arguments);

        return $this;
    }
}
