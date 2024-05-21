<?php

declare(strict_types=1);

namespace MoonShine\Support;

use Lcobucci\JWT\Encoder;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Random\RandomException;
use Throwable;

final class JWT
{
    public function __construct(
        private Encoder $encoder = new JoseEncoder,
    )
    {
    }

    public function parse(string $token): string|false
    {
        $parser = new Parser(
            $this->encoder
        );

        try {
            $parse = $parser->parse($token);

            if($parse->isExpired(now())) {
                return false;
            }

            return $parse
                ->claims()
                ->get('jti');
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @throws RandomException
     */
    public function create(string $identifiedBy): string
    {
        $builder = new Builder(
            $this->encoder,
            ChainedFormatter::default()
        );

        return $builder
            ->issuedAt(now()->toImmutable())
            ->expiresAt(now()->toImmutable()->addHour())
            ->identifiedBy($identifiedBy)
            ->getToken(new Sha256, InMemory::plainText(random_bytes(32)))
            ->toString();
    }
}
