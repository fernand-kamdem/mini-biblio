<?php

namespace Modules\Emprunt\Enums;

enum StatutEmprunt: string
{
    case Brouillon   = 'brouillon';
    case EnAttente   = 'en_attente';
    case Approuve    = 'approuve';
    case Refuse      = 'refuse';
    case Rendu       = 'rendu';
}