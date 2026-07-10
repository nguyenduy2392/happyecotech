<?php

namespace App\Support;

/**
 * Nguồn sự thật cho tên permission và ma trận role → permission.
 * Dùng chung bởi seeder và các policy để tránh magic string rải rác.
 */
class Rbac
{
    public const ROLES = ['SuperAdmin', 'Admin', 'Editor', 'Author', 'Viewer'];

    /** Nhóm resource nội dung (Editor được toàn quyền). */
    public const CONTENT_RESOURCES = [
        'service', 'team_member', 'post', 'post_category', 'tag', 'page', 'media', 'menu',
    ];

    /** Nhóm resource hệ thống. */
    public const SYSTEM_RESOURCES = [
        'contact', 'user', 'role', 'audit_log', 'site_setting',
    ];

    /** Các quyền cơ bản cho mọi resource. */
    public const BASE_ABILITIES = [
        'view_any', 'view', 'create', 'update', 'delete', 'restore', 'force_delete',
    ];

    /** Resource có khái niệm publish. */
    public const PUBLISHABLE = ['service', 'post', 'page', 'team_member'];

    /** @return string[] */
    public static function allResources(): array
    {
        return array_merge(self::CONTENT_RESOURCES, self::SYSTEM_RESOURCES);
    }

    /** @return string[] toàn bộ permission tồn tại trong hệ thống */
    public static function allPermissions(): array
    {
        $perms = [];

        foreach (self::allResources() as $resource) {
            foreach (self::BASE_ABILITIES as $ability) {
                $perms[] = "{$ability}_{$resource}";
            }
        }

        foreach (self::PUBLISHABLE as $resource) {
            $perms[] = "publish_{$resource}";
        }

        $perms[] = 'export_contact';
        $perms[] = 'manage_settings';

        return array_values(array_unique($perms));
    }

    /**
     * Ma trận role → permissions. SuperAdmin không liệt kê ở đây vì được
     * cấp toàn quyền qua Gate::before.
     *
     * @return array<string,string[]>
     */
    public static function roleMatrix(): array
    {
        $all = self::allPermissions();

        // Admin: mọi quyền trừ quản trị role (chỉ SuperAdmin) và mọi force delete.
        $admin = array_values(array_filter($all, function (string $perm): bool {
            if (in_array($perm, ['create_role', 'update_role', 'delete_role', 'restore_role', 'force_delete_role'], true)) {
                return false;
            }

            return ! str_starts_with($perm, 'force_delete_');
        }));

        // Editor: toàn quyền nội dung (không force delete) + đọc/cập nhật/xuất lead.
        $editor = [];
        foreach (self::CONTENT_RESOURCES as $resource) {
            foreach (['view_any', 'view', 'create', 'update', 'delete', 'restore'] as $ability) {
                $editor[] = "{$ability}_{$resource}";
            }
        }
        foreach (self::PUBLISHABLE as $resource) {
            $editor[] = "publish_{$resource}";
        }
        $editor = array_merge($editor, [
            'view_any_contact', 'view_contact', 'update_contact', 'export_contact',
            'view_any_audit_log', 'view_audit_log',
        ]);

        // Author: bài viết + media của chính mình (own được enforce trong PostPolicy).
        $author = [
            'view_any_post', 'view_post', 'create_post', 'update_post',
            'view_any_media', 'view_media', 'create_media',
            'view_any_post_category', 'view_post_category',
            'view_any_tag', 'view_tag', 'create_tag',
        ];

        // Viewer: chỉ đọc báo cáo/nội dung.
        $viewer = [];
        foreach (['service', 'team_member', 'post', 'page', 'contact', 'audit_log'] as $resource) {
            $viewer[] = "view_any_{$resource}";
            $viewer[] = "view_{$resource}";
        }

        return [
            'Admin' => array_values(array_unique($admin)),
            'Editor' => array_values(array_unique($editor)),
            'Author' => array_values(array_unique($author)),
            'Viewer' => array_values(array_unique($viewer)),
        ];
    }
}
