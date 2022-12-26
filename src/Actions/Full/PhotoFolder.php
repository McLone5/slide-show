<?php

declare(strict_types=1);

namespace App\Actions\Full;

use DomainException;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ParentLocationId;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\Repository\SearchService;

final class PhotoFolder
{
    public function __construct(
        private SearchService $searchService,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function __invoke(ContentView $view): ContentView
    {
        $location = $view->getLocation()
            ?? throw new DomainException('Location required');

        $view->addParameters([
            'photo_folder_list' => $this->getPhotoFolderList($location),
            'photo_list' => $this->getPhotoList($location),
        ]);
        return $view;
    }

    /**
     * @param Location $location
     * @return Location[]
     * @throws InvalidArgumentException
     */
    private function getPhotoFolderList(Location $location): array
    {
        $query = new LocationQuery([
            'query' => new LogicalAnd([
                new ParentLocationId($location->id),
                new ContentTypeIdentifier('photo_folder'),
            ]),
            'limit' => 100,
        ]);
        return array_map(
            static fn (SearchHit $searchHit) => ($searchHit->valueObject instanceof Location) ? $searchHit->valueObject : throw new DomainException('Unexpected value object type'),
            $this->searchService->findLocations($query)->searchHits
        );
    }

    /**
     * @param Location $location
     * @return Content[]
     * @throws InvalidArgumentException
     */
    private function getPhotoList(Location $location): array
    {
        $query = new LocationQuery([
            'query' => new LogicalAnd([
                new ParentLocationId($location->id),
                new ContentTypeIdentifier('photo'),
            ]),
            'limit' => 100,
        ]);

        return array_map(
            static fn (SearchHit $searchHit) => ($searchHit->valueObject instanceof Content) ? $searchHit->valueObject : throw new DomainException('Unexpected value object type'),
            $this->searchService->findContent($query)->searchHits
        );
    }
}
