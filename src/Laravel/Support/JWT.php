<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Support;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoder;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Random\RandomException;
use Throwable;

// todo move to package
final readonly class JWT
{
    public function __construct(
        private string $secret,
        private Encoder $encoder = new JoseEncoder(),
    ) {
    }

    public function parse(string $token): string|false
    {
        $parser = new Parser(
            $this->encoder
        );

        try {
            $parsedToken = $parser->parse($token);
            $key = InMemory::base64Encoded($this->secret);

            $configuration = Configuration::forSymmetricSigner(
                new Sha256(),
                $key
            );

            $configuration->setValidationConstraints(
                new SignedWith(
                    new Sha256(),
                    $key
                )
            );

            if(!$configuration->validator()->validate($parsedToken, ...$configuration->validationConstraints())) {
                // todo(jwt) exception
                return false;
            }

            if($parsedToken->isExpired(now())) {
                // todo(jwt) exception
                return false;
            }

            return $parsedToken
                ->claims()
                ->get('sub');
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @throws RandomException
     */
    public function create(string $id): string
    {
        $builder = new Builder(
            $this->encoder,
            ChainedFormatter::default()
        );

        return $builder
            ->issuedAt(now()->toImmutable())
            ->expiresAt(now()->toImmutable()->addHour())
            ->relatedTo($id)
            ->getToken(new Sha256(), InMemory::base64Encoded($this->secret))
            ->toString();
    }
}
